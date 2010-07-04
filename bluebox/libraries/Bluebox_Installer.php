<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Bluebox_Configure.php - Classes to manage module installation, removal, and activation.
 * Created on Jun 2, 2009
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Core
 */
/**
 INSTALLS     UPGRADES     DOWNGRADES     REPAIR       ENABLE       DISABLE       UNINSTALL
 --------------------------------------------------------------------------------------------
 verify	     verify       verify         verify
 preinstall   preupgrade   predowngrade                                          preuninstall
 install      upgrade      downgrade      repair       enable       disable      uninstall
 postinstall  postupgrade  postdowngrade                                         postuninstall
 sanityCheck  sanityCheck  sanityCheck    sanityCheck                            sanityCheck
 */
class Bluebox_Installer
{
    //class Bluebox_PackageManager?

    /**
     * These are a list of category name constants for use in
     * Bluebox_Configure $category
     */
    const TYPE_DEFAULT = 'TYPE_DEFAULT';
    const TYPE_MODULE = 'TYPE_MODULE';
    const TYPE_PLUGIN = 'TYPE_PLUGIN';
    const TYPE_DRIVER = 'TYPE_DRIVER';
    const TYPE_SERVICE = 'TYPE_SERVICE';
    const TYPE_DIALPLAN = 'TYPE_DIALPLAN';
    const TYPE_ENDPOINT = 'TYPE_ENDPOINT';
    /**
     * @var array This var tracks errors internally until the current operation is complete
     */
    public static $errors = array();
    /**
     * @var array This var tracks warnings internally until the current operation is complete
     */
    public static $warnings = array();
    /**
     * @var object Singleton instance
     */
    protected static $instance = NULL;
    /**
     * @var array During module installation this allows the various procedures to access the same array
     */
    private static $packages = array();
    /**
     *
     * @var <type>
     */
    public static $configurations = array();

    private static $telephonyDriver = NULL;
    /**
     * This function creates the singelton instance and resets any
     * errors or warnings
     *
     * @return void
     */
    public static function init()
    {
        self::$errors = self::$warnings = array();
        if (empty(Bluebox_Installer::$instance)) {
            Bluebox_Installer::$instance = new Bluebox_Installer();
        }
    }
    /**
     * This function scans the bluebox module path for configure files and returns a list
     * of packages.
     *
     * @return array
     * @param string $category[optional]
     * @param bool $ignoreDB[optional] If this is set to true then we will not consider the installed modules
     */
    public static function listPackages($filter = array() , $ignoreDB = false)
    {
        // Reset the error and warning vars, also create a instance if it doesnt exist
        self::init();

        // Initialize a empty packages array
        $packages = array();
        // Check if there are any filters
        if (!empty($filter)) {
            // If we are only supplied with a string then assume it is a inlcude filter
            if (is_string($filter)) {
                $filter = array(
                    'include' => array(
                        $filter
                    )
                );
                // If we are given a one dimentional array then assume it is also an include filter

            } else if (is_array($filter) && empty($filter['include']) && empty($filter['exclude'])) {
                $filter = array(
                    'include' => $filter
                );
            }
            // Ensure are sub-keys are arrays themselfs
            if (!empty($filter['include']) && is_string($filter['include'])) $filter['include'] = array(
                $filter['include']
            );
            if (!empty($filter['exclude']) && is_string($filter['exclude'])) $filter['exclude'] = array(
                $filter['exclude']
            );
        }
        if (!is_array($filter)) $filter = array();
        // Merge the corrected filters array with the defaults to fill in any blanks
        $filter = array_merge(array(
            'include' => array() ,
            'exclude' => array()
        ) , $filter);
        // Get a list of what's already installed. Defaults to modules, but can be languages, telephony drivers, etc.
        $configurations = glob(MODPATH . '*/configure.php', GLOB_MARK);
        // Run through all the config files and include them
        foreach($configurations as $configuration) {
            // Add what we think might be a configure.php file
            $declaredBefore = get_declared_classes();
            require_once ($configuration);
            // Get the last added class
            $declaredAfter = get_declared_classes();
            if (count($declaredBefore) == count($declaredAfter)) {
                continue;
            }
            $foundClass = end($declaredAfter);
            // Check if we found a Bluebox_Configure class
            if ($foundClass && is_subclass_of($foundClass, 'Bluebox_Configure')) {
                // If we have found a configure class add it to the list
                self::$configurations[$foundClass] = $configuration;
            }
        }
        // Run down the array of configuration classes that where found during this session
        foreach(self::$configurations as $class => $configuration) {
            // Get a list of all the static vars of this class
            $packageVars = get_class_vars($class);
            // If the moduleName is empty use the class name
            if (empty($packageVars['packageName'])) $packageVars['packageName'] = str_replace('_Configure', '', $class);
            // moduleName is used in a lot of places, and I am lazy ;)
            $packageName = $packageVars['packageName'];
            // If this module is already been loaded into the modules array skip the rest of this
            if (array_key_exists($packageName, $packages)) continue;
            // If there is no displayName specified then attempt to make a pretty version of moduleName
            if (empty($packageVars['displayName'])) $packageVars['displayName'] = ucfirst(inflector::humanize($packageName));
            // make sure the defualt value is a bool
            if (!is_bool($packageVars['default'])) $packageVars['default'] = false;
            // make sure to save the directory to the module arrays
            $packageVars['directory'] = dirname(str_replace(MODPATH, '', $configuration));
            // Standardize the type list
            if (!defined('Bluebox_Installer::' . $packageVars['type'])) $packageVars['type'] = Bluebox_Installer::TYPE_DEFAULT;
            if ($packageVars['type'] == Bluebox_Installer::TYPE_DEFAULT) {
                kohana::log('alert', 'Module ' . $packageName . ' is using the default package type!');
            }
            // Standardize the required list as an array, assuming they ment core if only a string
            $packageVars['required'] = is_string($packageVars['required']) ? array(
                'core' => $packageVars['required']
            ) : $packageVars['required'];
            // Save the name of the configuration class
            $packageVars['configureClass'] = $class;
            // Default installedAs and action
            $packageVars['installedAs'] = FALSE;
            $packageVars['action'] = FALSE;
            // if the navStructures array is missing or not an array build it from the individual values
            if (!isset($packageVars['navStructures']) || !is_array($packageVars['navStructures'])) {
                if (!is_null($packageVars['navURL'])) {
                    $packageVars['navStructures'] = array(array_intersect_key(
                        $packageVars,
                        array_flip(array('navBranch', 'navURL', 'navLabel', 'navSummary', 'navSubmenu'))
                    ));
                } else {
                     kohana::log('alert', 'Package ' . $packageName . ' does not have any valid navigation defined.');
                }
            }
            // if the navStructures is an array make sure it is in the correct format
            if (isset($packageVars['navStructures'])) {
                if (!array_key_exists(0, $packageVars['navStructures'])) {
                    $packageVars['navStructures'] = array($packageVars['navStructures']);
                }
                foreach ($packageVars['navStructures'] as $key => $navStructure){
                    if (empty($navStructure['navURL'])) {
                        kohana::log('error', 'Package ' . $packageName . ' defined an invalid navigation!');
                        unset($packageVars['navStructures'][$key]);
                        continue;
                    }
                    if (empty($navStructure['navLabel'])) {
                        $packageVars['navStructures'][$key]['navLabel'] = $packageVars['displayName'];
                    }
                    if (empty($navStructure['navSummary'])) {
                        $packageVars['navStructures'][$key]['navSummary'] = $packageVars['summary'];
                    }
                    if (empty($navStructure['navBranch'])) {
                        $packageVars['navStructures'][$key]['navBranch'] = '/';
                    }
                    if (!isset($navStructure['navSubmenu'])) {
                        $packageVars['navStructures'][$key]['navSubmenu'] = array();
                    } else if (!is_array($navStructure['navSubmenu'])) {
                        kohana::log('error', 'Package ' . $packageName . ' defined an invalid submenu!');
                        $packageVars['navStructures'][$key]['navSubmenu'] = array();
                    } else {
                        $submenuItems = array();
                        foreach ($navStructure['navSubmenu'] as $name => $submenu) {
                            if (is_string($submenu)) $submenu = array ('url' => $submenu);
                            if (empty($submenu['url'])) {
                                kohana::log('error', 'Package ' . $packageName . ' defined an invalid submenu item!');
                                continue;
                            } else {
                                $submenuItem = &$submenuItems[$name];
                                $submenuItem['url'] = $submenu['url'];
                            }
                            if (empty($submenu['disabled'])) {
                                $submenuItem['disabled'] = FALSE;
                            } else {
                                $submenuItem['disabled'] = TRUE;
                            }
                            if (trim($submenuItem['url'], '/') == trim($navStructure['navURL'], '/')) {
                                $submenuItem['entry'] = TRUE;
                            } else {
                                $submenuItem['entry'] = FALSE;
                            }
                        }
                        $packageVars['navStructures'][$key]['navSubmenu'] = $submenuItems;
                    }
                }
            } else {
                $packageVars['navStructures'] = array();
            }
            // we dont need these anymore
            unset($packageVars['navIcon']);
            unset($packageVars['navBranch']);
            unset($packageVars['navURL']);
            unset($packageVars['navLabel']);
            unset($packageVars['navSummary']);
            unset($packageVars['navSubmenu']);
            // Check if there are any exlcude filters and apply them
            if (!empty($filter['exclude']) && in_array($packageVars['type'], $filter['exclude'])) {
                continue;
            }
            // Check if there are any include filters and apply them
            if (!empty($filter['include']) && !in_array($packageVars['type'], $filter['include'])) {
                continue;
            }
            // Get a directory listing of the php files in models for this package
            $possibleModels = glob(MODPATH . $packageVars['directory'] . '/models/*.php', GLOB_MARK);
            // Attempt to load any models that belong to this module
            if (!empty($possibleModels)) {
                $packageVars['models'] = Doctrine::loadModels(MODPATH . $packageVars['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
            } else {
                $packageVars['models'] = array();
            }
            // If we got to this point save the vars
            $packages[$packageName] = $packageVars;
            // Skip checking the DB if we dont need to
            if ($ignoreDB) continue;
            // Check if this module is already installed
            $q = Doctrine_Query::create();
            $q->from('Module m');
            $q->where('m.name = ?', $packageName);
            // Save the query results in the array
            try {
                $results = $q->execute(array() , Doctrine::HYDRATE_ARRAY);
                $packages[$packageName]['installedAs'] = reset($results);
                // If the module is already disabled/enabled copy that into default
                $enabled = $packages[$packageName]['installedAs']['enabled'];
                if (is_bool($enabled)) $packages[$packageName]['default'] = $enabled;
            }
            catch(Doctrine_Connection_Exception $e) {
                $results = array();
            }
        }
        return $packages;
    }
    /**
     * This function can be used to create a catalog from a system
     * for use as a base template to create a repository.
     *
     * TODO: It would be neat if this could be used to 'setup' a system based
     * on another...
     *
     * @return string
     */
    public static function createCatalog() {
        $packages = self::listPackages(NULL, TRUE);

        $xml = new SimpleXMLElement('<?xml version=\'1.0\' standalone=\'yes\'?><modules></modules>');

        foreach ($packages as $packageName => $package) {
            $module = $xml->addChild('module');
            $module->addAttribute('date', date('U'));
            $module->addAttribute('name', $packageName);

            $parameters = array_diff_key($package, array_flip(
                array('directory', 'configureClass', 'installedAs', 'action')
            ));

            self::_xmlChildMixed($parameters, $packageName, $module);
        }

        return $xml->asXML();
    }
    /**
     * Scans a repository catalog file and compares the versions avaliable
     * to the ones on the system.  It will modify the provided packages array.
     *
     * @param array Pacakge list of on the current system
     * @param string The URL of the remote catalog
     * @param bool If the remote catalog is in cache dont seek it again
     */
    public static function checkForUpdates(&$packages, $url, $checkRemote = TRUE) {
        $cache = Cache::instance();

        $xml = $cache->get(md5($url));

        if (empty($xml) && $checkRemote === TRUE) {
            $remoteModTime = self::_getRemoteLastModified($url);
            if (!empty($remoteModTime)) {
                kohana::log('debug', 'Retrieve catalog for update check for the first time');
                $xml = simplexml_load_file($url);
                $xml->addChild('retrieved', $remoteModTime);
                $cache->set(md5($url), $xml->asXML(), 'update_catalog', 0);
            }
        } else {
            $xml = new SimpleXMLElement($xml);

            if ($checkRemote === TRUE) {
                $currentModTime = (int)$xml->retrieved;
                $remoteModTime = self::_getRemoteLastModified($url);

                if($currentModTime != $remoteModTime && !empty($remoteModTime)) {
                    kohana::log('debug', 'Repo has a new catalog');

                    $xml = simplexml_load_file($url);
                    $xml->addChild('retrieved', $remoteModTime);
                    $cache->set(md5($url), $xml->asXML(), 'update_catalog', 0);
                } else {
                    kohana::log('debug', 'Cached catalog is up-to-date');
                }
            }
        }

        if (!empty($xml->retrieved)) {
            kohana::log('debug', 'Comparing packages to catalog');
            foreach ($xml as $module) {
                $packageName = $module->attributes()->name;

                if (empty($packageName)) {
                    continue;
                }

                $packageName = (string)$packageName;

                if (empty($packages[$packageName])) {
                    continue;
                }
                $package = $packages[$packageName];

                $avaliableVersion = $module->version;
                settype($avaliableVersion, gettype($package['version']));

                if (self::_compareVersions($package['version'], $avaliableVersion, '>')) {
                    $packages[$packageName]['updateAvaliable'] = $avaliableVersion;

                    $updateUrl = (string)$module->updateURL;

                    if (empty($updateUrl)) {
                        $packages[$packageName]['updateURL'] = $url;
                    } else {
                        $packages[$packageName]['updateURL'] = $updateUrl;
                    }
                    kohana::log('debug', 'Found update for ' .$packageName .' ' .$package['version'] .' -> ' .$avaliableVersion);
                }
            }
        }
        foreach ($packages as $packageName => $packagex) {
            if (!empty($packagex['updateAvaliable'])) {
                continue;
            }
            $packages[$packageName]['updateAvaliable'] = FALSE;
            $packages[$packageName]['updateURL'] = NULL;
        }
    }
    /**
     * This function checks if a package is Avaliable in the list of packages
     *
     * returns
     * -1 - Package not in the list
     *  0 - Not installed or schedualed for uninstall
     *  1 - Package in the list but is disabled
     *  2 - Package avaliable and enabled
     */
    public static function packageAvailable($packages, $name)
    {
        /**
         * TODO: This behavior means that packages that have depends outside their catagories
         *       can not be passed to checkDependencies with a filtered list.... hmmmm
         */
        // TEST: Package is missing from the list
        if (!array_key_exists($name, $packages)) return -1;
        $package = $packages[$name];
        $action = $package['action'];
        $status = - 1;
        // If the package is scheduled for installation
        $installActions = array(
            'downgrade',
            'install',
            'upgrade'
        );
        if (in_array($action, $installActions)) {
            // TEST: Package is to be installed but will be deactive
            if (!$package['default']) $status = 1;
            // TEST: Package is to be installed and will be active
            if ($package['default']) $status = 2;
        }
        // If it is not installed and (from above) not going to be or is going to uninstalled
        else if (empty($package['installedAs']) || $action == 'uninstall') {
            $status = 0;
        }
        // If it is installed (from above) but not enabled
        else if (empty($package['installedAs']['enabled'])) {
            // Check if it will be enabled this go round
            if ($action == 'enable') $status = 2;
            else $status = 1;
        } else {
            // Make sure it will not be disabled on this pass
            if ($action == 'disable') $status = 1;
            else $status = 2;
        }
        // TEST: Package has an action but will not preform it due to errors
        if (!empty(self::$errors[$name]) && !empty($action)) {
            $packages[$name]['action'] = false;
            return self::packageAvailable($packages, $name);
        } else {
            return $status;
        }
    }
    /**
     * This function returns an array showing how all the modules inter-relate
     *
     * @return array an representation of the packages relationships
     * @param array $packages is a list of packages to work on
     */
    public static function relationTree($packages)
    {
        // If we are not provided with a packages array then return empty handed
        if (empty($packages) || !is_array($packages)) {
            self::$errors[] = 'No packages provided for relationship mapping';
            return array();
        }
        $relationTree = array();
        foreach($packages as $name => $package) {
            // If this package does not have a default array in the tree load one
            if (!isset($relationTree[$name])) $relationTree[$name] = array(
                'dependOn' => array() ,
                'dependOf' => array()
            );
            // If there are no requirements then test the next
            if (empty($package['required'])) continue;
            // For each package in our array loop through them and build the relationships
            foreach($package['required'] as $required => $version) {
                $logicOperator = array(
                    'or',
                    'not',
                    'xor'
                );
                if (in_array($required, $logicOperator)) continue;
                if (strtolower($required) != 'core') {
                    // The parent has a default array but we need to set one on the child
                    // since we are about to populate the array the if empty above will fail
                    if (!isset($relationTree[$required])) $relationTree[$required] = array(
                        'dependOn' => array() ,
                        'dependOf' => array()
                    );
                    // Load the arrays of the parent and child
                    $relationTree[$name]['dependOn'][] = $required;
                    $relationTree[$required]['dependOf'][] = $name;
                }
            }
        }
        return $relationTree;
    }
    /**
     * This funcion determines if a package will be installed,
     * downgraded, or upgraded from the current version.
     *
     * @return array the packages array with additional actions
     * @param array $module
     */
    public static function determineActions($packages, $install = NULL)
    {
        // Reset the error and warning vars, also create a instance if it doesnt exist
        self::init();
        // If there are no values in the install list then the
        // packages are left as they where
        if (is_null($install)) $install = array();
        else
        // Standardize the install list as an array
        $install = is_string($install) ? array(
            $install
        ) : $install;
        foreach($packages as $name => $package) {
            // If there is already a valid action specified then dont override it
            if (self::_isValidAction($package['action'])) {
                continue;
            } else {
                $ptPackage = & $packages[$name];
                $ptPackage['action'] = false;
            }
            // If this package doesnt already have an action
            // and it is in the install list or the insall list is 'all'
            if (in_array($name, $install)) {
                // If this package is not already installed then install
                if (empty($package['installedAs'])) {
                    $ptPackage['action'] = 'install';
                    continue;
                }
                // Make a determination it the modules will be upgraded, installed, or downgraded
                if (self::_compareVersions($package['installedAs']['module_version'], $package['version'], '>')) $ptPackage['action'] = 'upgrade';
                else if (self::_compareVersions($package['installedAs']['module_version'], $package['version'], '<')) $ptPackage['action'] = 'downgrade';
                else $ptPackage['action'] = 'install';
            }
        }
        return $packages;
    }
    /**
     * This function will check if a packages dependencies can be met.  This involes running an
     * _check methods in the configure as well as resolving the $required array
     *
     * @return array of packages
     * @param array $packages is a list of all $packages
     * @param array $install the key of the $packages that are schedualed for installation
     *
     */
    public static function checkDependencies($packages = NULL, $install = NULL)
    {
        // Reset the error and warning vars, also create a instance if it doesnt exist
        self::init();
        // If we are not supplied with a list of packages then get one
        if (is_null($packages)) $packages = self::listPackages();
        // If we still dont have a valid packages list then get out of here!
        if (empty($packages) || !is_array($packages)) {
            self::$errors[] = 'Unable to find packages!';
            return false;
        }
        // determine any actions to take for each key in $install
        $packages = self::determineActions($packages, $install);
        // For the appropriate actions run the check methods
        self::_runCheckMethods($packages);
        // Ensure that the required array can be fulfilled
        foreach(array_keys($packages) as $name) {
            $packages = self::_fulfillRequired($packages, $name);
            $packages = self::_abandonRequired($packages, $name);
        }
        return $packages;
    }
    /**
     * Cycle through a list of modules, instantiate them, then install or upgrade them.
     *
     * NOTE: Having one method for install and/or upgrade allows us to recover from crashes during installation,
     * and provides a safety net from accidentally running damaging install commands when we should have done an upgrade
     * to preserve existing data.
     */
    public function processActions($packages = NULL, $install = NULL)
    {
        // Reset the error and warning vars, also create a instance if it doesnt exist
        self::init();
        // Make sure we are ready to install
        self::$packages = self::checkDependencies($packages, $install);
        // If we still dont have a valid packages list then get out of here!
        if (empty($packages) || !is_array($packages)) {
            self::$errors[] = 'Unable to find packages!';
            return false;
        }
        // If we found an error stop the install
        if (!empty(self::$errors)) return false;
        // It is possible that checkDependencies added warnings, so clear those
        self::$warnings = array();
        // This ensures that any modules that depend on another
        // are loaded after their dependencies!
        self::_dependencySort(self::$packages);
        // Instantiate the package configurations with actions to preform
        foreach(self::$packages as $name => $package) {
            if ($package['action']) self::$packages[$name]['instance'] = new $package['configureClass'];
        }
        // Verify all modules are intact
        self::verify();
        // Execute all the pre-action methods
        self::preActions();
        // Execute all the action methods
        self::actions();
        // Execute all the post-action methods
        self::postActions();
        // Execute all the sanityCheck methods
        self::sanityCheck();
        // This internal function lets us keep default finilize behavor
        // Such as determining if a modules dependencies installed sucessfully....
        self::finalize();
        // If we were successful, execute all the global onSuccessInstall/onSuccessUpgrade/onSuccessXXXX methods
        if (empty(self::$errors)) {
            self::completed();
        }
        self::$packages = array();
        return empty(self::$errors);
    }
    /**
     * This function sorts the packages such that those that have errors
     * are on top
     *
     * @return void
     * @param array $packages
     */
    public function errorSort(&$packages)
    {
        if (!is_array($packages) || empty(self::$errors)) return $packages;
        $errors = array();
        foreach(array_keys(self::$errors) as $error) {
            if (!empty($packages[$error])) {
                $package = array(
                    $error => $packages[$error]
                );
                unset($packages[$error]);
                $errorMsg = self::$errors[$error];
                if (stristr(reset($errorMsg), 'This package relies on')) {
                    $errors = array_merge($errors, $package);
                } else {
                    array_reverse($errors);
                    $errors = array_merge($errors, $package);
                    array_reverse($errors);
                }
            }
        }
        $packages = array_merge($errors, $packages);
    }
    /**
     * This function sorts the packages such that those that have warnings
     * are on top
     *
     * @return void
     * @param array $packages
     */
    public function warningSort(&$packages)
    {
        if (!is_array($packages) || empty(self::$warnings)) return $packages;
        foreach(array_keys(self::$warnings) as $warning) {
            if (!empty($packages[$warning])) {
                $package = array(
                    $warning => $packages[$warning]
                );
                unset($packages[$warning]);
                $packages = array_merge($package, $packages);
            }
        }
    }
    private function rollback($name)
    {
        self::$packages[$name]['action'] = false;
        self::$packages[$name]['instance'] = false;
    }
    private function verify()
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'repair',
            'verify'
        );
        foreach(self::$packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], $runForActions)) {
                try {
                    $result = call_user_func(array(
                        $package['instance'],
                        'verify'
                    ) , $package);
                    if (self::_setIssue($result, $name)) self::rollback($name);
                }
                catch(Exception $e) {
                    self::_setError($e->getMessage() , $name);
                    self::rollback($name);
                }
            }
        }
    }
    private function preActions()
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'uninstall'
        );
        foreach(self::$packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], $runForActions)) {
                try {
                    $result = call_user_func(array(
                        $package['instance'],
                        'pre' . $package['action']
                    ) , $package);
                    if (self::_setIssue($result, $name)) self::rollback($name);
                }
                catch(Exception $e) {
                    self::_setError($e->getMessage() , $name);
                    self::rollback($name);
                }
            }
        }
    }
    private function actions()
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'repair',
            'enable',
            'disable',
            'uninstall'
        );
        $newModules = array();

        foreach(self::$packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], $runForActions)) {
                Kohana::log('debug', 'Installer processing ' . $package['action'] . ' on ' . $package['packageName']);
                try {
                    $result = call_user_func(array(
                        $package['instance'],
                        $package['action']
                    ) , $package);
                    if (self::_setIssue($result, $name)) self::rollback($name);
                }
                catch(Exception $e) {
                    self::_setError($e->getMessage() , $name);
                    self::rollback($name);
                }
            }
        }

        // for the following actions load up any dependiencys
        $loadDependsFor = array (
            'downgrade',
            'install',
            'upgrade',
            'uninstall'
        );
        // get a list of relationships
        $relationTree = self::relationTree(self::$packages);
        // get a list of the modules that are already loaded
        $loadedModules = Kohana::config('core.modules');
        // init an array of new modules we need kohana to be aware of
        $loadModule = array();
        // run through all the packages
        foreach (self::$packages as $name => $package) {
            // if the pacakge doesnt have an action skip
            if (!in_array($package['action'], $loadDependsFor)) continue;
            // if the package doesnt depend on anther module skip
            if (empty($relationTree[$name]['dependOn'])) continue;
            // for each module that this package depends on see if it is loaded
            foreach ($relationTree[$name]['dependOn'] as $dependOf) {
                // if the module is already loaded the skip
                if (array_key_exists($dependOf, $loadedModules)) continue;
                // put this module into our list of new modules
                kohana::log('alert', 'Loading module ' . $dependOf . ' to met dependency of ' . $name);
                $loadModule[$dependOf] = MODPATH . self::$packages[$dependOf]['directory'];
                // see if this module has models we should be worried about
                if (is_dir(MODPATH . self::$packages[$dependOf]['directory'] . '/models')) {
                    // Note that with MODEL_LOADING_CONSERVATIVE set, the model isn't really loaded until first requested
                    Doctrine::loadModels(MODPATH . self::$packages[$dependOf]['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
                }
            }
        }
        // if there are any modules that are not already loaded that we may need
        // then load them into kohana now
        if (!empty($loadModule)) {
            $modules = array_unique(array_merge($loadedModules, $loadModule));
            Kohana::config_set('core.modules', $modules);
        }

    }
    private function postActions()
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'uninstall'
        );
        foreach(self::$packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], $runForActions)) {
                try {
                    $result = call_user_func(array(
                        $package['instance'],
                        'post' . ucfirst($package['action'])
                    ) , $package);
                    if (self::_setIssue($result, $name)) self::rollback($name);
                }
                catch(Exception $e) {
                    self::_setError($e->getMessage() , $name);
                    self::rollback($name);
                }
            }
        }
    }
    private function sanityCheck()
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'repair'
        );
        foreach(self::$packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], $runForActions)) {
                try {
                    $result = call_user_func(array(
                        $package['instance'],
                        'sanityCheck'
                    ) , $package);
                    if (self::_setIssue($result, $name)) self::rollback($name);
                }
                catch(Exception $e) {
                    self::_setError($e->getMessage());
                    self::rollback($name);
                }
            }
        }
    }
    private function completed()
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'uninstall'
        );
        foreach(self::$packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], $runForActions)) {
                try {
                    $result = call_user_func(array(
                        $package['instance'],
                        'completed' . ucfirst($package['action'])
                    ) , $package);
                    if (self::_setIssue($result, $name)) self::rollback($name);
                }
                catch(Exception $e) {
                    self::_setError($e->getMessage());
                }
            }
        }
    }
    /**
     * This function is designed to handle any INTERNAL routines that need
     * to be executed after the package has completed everything it may possible do...
     */
    private function finalize()
    {
        Bluebox_Installer::disableTelephony();

        foreach(self::$packages as $name => $package) {
            switch ($package['action']) {
            case 'downgrade':
            case 'install':
            case 'upgrade':
                // If this module had errors during install DONT ADD IT
                if (!empty(self::$errors[$name])) break;

                try {
                    // If this module is already installed
                    if (!empty($package['installedAs']['module_id'])) {
                        Kohana::log('debug', 'Updating ' . $package['packageName'] . ' in Module');
                        $q = Doctrine::getTable('Module')->createQuery('m')->where('m.module_id = ?', $package['installedAs']['module_id']);
                        $dbModule = $q->fetchOne();
                        // If we coulnt get the current row then we will make a new one
                        if (!$dbModule) {
                            Kohana::log('alert', 'Failed to find ' . $package['packageName'] . ' in Module, adding new module');
                            $dbModule = new Module;
                        }
                    } else {
                        Kohana::log('debug', 'Adding ' . $package['packageName'] . ' to Module');
                        // Get a new row for the module
                        $dbModule = new Module;
                    }
                    // Insert or update (see above) the new modules info!
                    $dbModule->name = $package['packageName'];
                    $dbModule->display_name = $package['displayName'];
                    $dbModule->module_version = $package['version'];
                    $dbModule->enabled = self::_verifyDependencyInstall($package);
                    $dbModule->basedir = $package['directory'];
                    $dbModule->parameters = array_diff_key($package, array(
                        'action' => 0,
                        'instance' => 0,
                        'installedAs' => 0
                    ));
                    $dbModule->save();
                    self::_integrateNumberType($package['models'], $dbModule, $name);
                    $dbModule->free(TRUE);
                }
                catch(Exception $e) {
                    self::_setError('Unable to integrate ' . $package['packageName'] . ' into Module! <br /><small>' . $e->getMessage() . '</small>', $name);
                    break;
                }
                break;

            case 'repair':
                // If this module had errors during repair skip this
                if (!empty(self::$errors[$name])) break;

                try {
                    // If this module is already installed
                    if (!empty($package['installedAs']['module_id'])) {
                        Kohana::log('debug', 'Updating ' . $package['packageName'] . ' in Module');
                        $q = Doctrine::getTable('Module')->createQuery('m')->where('m.module_id = ?', $package['installedAs']['module_id']);
                        $dbModule = $q->fetchOne();
                        // If we coulnt get the current row then we will make a new one
                        if (!$dbModule) {
                            Kohana::log('alert', 'Failed to find ' . $package['packageName'] . ' in Module, adding new module');
                            $dbModule = new Module;
                        }
                    } else {
                        Kohana::log('debug', 'Adding ' . $package['packageName'] . ' to Module');
                        // Get a new row for the module
                        $dbModule = new Module;
                    }
                    // Insert or update (see above) the new modules info!
                    $dbModule->name = $package['packageName'];
                    $dbModule->display_name = $package['displayName'];
                    $dbModule->module_version = $package['version'];
                    $dbModule->basedir = $package['directory'];
                    $dbModule->parameters = array_diff_key($package, array(
                        'action' => 0,
                        'instance' => 0,
                        'installedAs' => 0
                    ));
                    $dbModule->save();
                    self::_integrateNumberType($package['models'], $dbModule, $name);
                    $dbModule->free(TRUE);
                }
                catch(Exception $e) {
                    self::_setError('Unable to integrate ' . $package['packageName'] . ' into Module! <br /><small>' . $e->getMessage() . '</small>', $name);
                    break;
                }
                break;

            case 'enable':
                if (!empty(self::$errors[$name])) break;

                Kohana::log('debug', 'Update Module to enable ' . $package['packageName']);
                try {
                    $dbModule = Doctrine::getTable('Module')->find($package['installedAs']['module_id']);
                    if (!$dbModule) {
                        self::_setError('Unable to locate the DB entry for ' . $package['displayName'], $name);
                    } else {
                        $dbModule->enabled = true;
                        $dbModule->save();
                        self::_integrateNumberType($package['models'], $dbModule, $name);
                        $dbModule->free(TRUE);
                    }
                }
                catch(Exception $e) {
                    self::_setError('Error during update of ' . $package['displayName'] . ' : ' . $e->getMessage() , $name);
                }
                break;

            case 'disable':
                if (!empty(self::$errors[$name])) break;

                Kohana::log('debug', 'Update Module to disable ' . $package['packageName']);
                try {
                    $dbModule = Doctrine::getTable('Module')->find($package['installedAs']['module_id']);
                    if (!$dbModule) {
                        self::_setError('Unable to locate the DB entry for ' . $package['displayName'], $name);
                    } else {
                        $dbModule->enabled = false;
                        $dbModule->save();
                        self::_removeNumberType($package['models'], $name);
                        $dbModule->free(TRUE);
                    }
                }
                catch(Exception $e) {
                    self::_setError('Error during update of ' . $package['displayName'] . ' : ' . $e->getMessage() , $name);
                }
                break;

            case 'uninstall':
                if (!empty(self::$errors[$name])) break;

                Kohana::log('alert', 'Removing ' . $package['packageName'] . ' from Module');
                try {
                    $dbModule = Doctrine::getTable('Module')->find($package['installedAs']['module_id']);
                    if (!$dbModule) {
                        self::_setError('Unable to locate the DB entry for ' . $package['displayName'], $name);
                    } else {
                        $dbModule->delete();
                        self::_removeNumberType($package['models'], $name);
                        $dbModule->free(TRUE);
                    }
                }
                catch(Exception $e) {
                    self::_setError('Error during removal of ' . $package['displayName'] . ' : ' . $e->getMessage() , $name);
                }
                break;
            }
        }

        Bluebox_Installer::restoreTelephony();
    }
    /**
     * This function will attemp to integrate a number type if a model extends
     * Number
     *
     * @return bool
     * @param array $models
     */
    private static function _integrateNumberType($models, $dbModule, $name = '')
    {
        foreach($models as $model) {
            if (!class_exists($model) || !is_subclass_of($model, 'Number')) continue;
            Kohana::log('debug', 'Adding ' . $model . ' to NumberType');
            try {
                $q = Doctrine::getTable('NumberType')->createQuery('n')->where('n.class = ?', $model);
                $numberType = $q->fetchOne();
                // If we coulnt get the current row then we will make a new one
                if (!$numberType) {
                    Kohana::log('debug', 'Failed to find ' . $model . ' in NumberType, adding new number type');
                    $numberType = new NumberType();
                    $numberType->class = $model;
                }
                $numberType->Module = $dbModule;
                $numberType->save();
                $numberType->free(TRUE);
            }
            catch(Exception $e) {
                self::_setError('Unable to integrate ' . $model . ' into NumberType! <br /><small>' . $e->getMessage() . '</small>', $name);
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * This function will attempt to remove a number type
     *
     * @return bool
     * @param array $models
     */
    private static function _removeNumberType($models, $name = '')
    {
        foreach($models as $model) {
            if (!class_exists($model) || !is_subclass_of($model, 'Number')) continue;
            Kohana::log('debug', 'Removing ' . $model . ' from NumberType after disable');
            try {
                $q = Doctrine::getTable('NumberType')->createQuery('n')->where('n.class = ?', $model);
                $numberType = $q->fetchOne();
                // If we have a row then remov it
                if ($numberType) {
                    $numberType->delete();
                    $numberType->free(TRUE);
                }
            }
            catch(Exception $e) {
                self::_setError('Unable to remove ' . $model . ' from NumberType! <br /><small>' . $e->getMessage() . '</small>', $name);
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * This function sorts the modules to be installed so they
     * are always bellow any required dependencies
     *
     * @return bool
     * @param array $modules
     * @param string $key
     */
    public static function _dependencySort(&$packages)
    {
        // Reset the packages array so we start at the beging
        reset($packages);
        // This is a saftey stop, otherwise it may never end it the depends are complicated enough
        $resetCount = 0;
        // We can not use foreach here....
        while (list($name, $package) = each($packages)) {
            // This package has no action, has not requirements or the requirements arent an array move on
            if (empty($package['action']) || empty($package['required']) || !is_array($package['required'])) continue;
            // Get a list of package keys
            $packageList = array_flip(array_keys($packages));
            // Determine the minimuim acceptable posistion for this package
            // (ie. were is the lowest, not installed, required package in the array)
            $minPos = 0;
            foreach($package['required'] as $required => $version) {
                $skip = array(
                    'or',
                    'not',
                    'xor',
                    'core'
                );
                if (in_array($required, $skip)) continue;
                if (!empty($packages[$required]['action'])) $minPos = $packageList[$required] >= $minPos ? $packageList[$required] : $minPos;
            }
            // Determine our position
            $packagePos = $packageList[$name];
            // If we are further down in the array then any non-installed
            // dependency then all is well, next!
            if ($packagePos >= $minPos) continue;
            // Shift everything in the array accordingly
            if ($minPos >= count($packageList)) {
                $middle = array_splice($packages, $packagePos, 1);
                $modules = array_merge($packages, $middle);
            } else {
                $middle = array_splice($packages, $packagePos, 1);
                $top = array_slice($packages, 0, $minPos);
                $bottom = array_slice($packages, $minPos);
                $packages = array_merge($top, $middle, $bottom);
            }
            // Make the whole loop repeat incase we just moved
            // bellow a package depending on us...
            if ($resetCount < (count($packages) ^ 2)) {
                $resetCount+= 1;
                reset($packages);
            } else {
                break;
            }
        }
    }
    public static function disableTelephony() {
        if (is_null(self::$telephonyDriver)) {
            self::$telephonyDriver = Kohana::config('telephony.driver');
        }
        Kohana::config_set('telephony.driver', FALSE);
    }
    public static function restoreTelephony() {
        if (!is_null(self::$telephonyDriver)) {
            Kohana::config_set('telephony.driver', self::$telephonyDriver);
        }
    }
    /**
     * This attempts to set an error or warning from a mixed $result
     *
     * @return bool True if error otherwise false
     * @param bool|string|array $result[optional] This is the mixed var to operate on, false or a string are assumed errors
     * @param object $name[optional] The name of the module responsible for this issue
     * @param object $key[optional] The anme of this issue
     */
    private static function _setIssue($result = true, $name = '', $key = '')
    {
        // If we are given FALSE then assume it is an error with no message
        if (is_bool($result) && !$result) {
            self::_setError('Unspecified error!', $name, 'generic');
            return true;
        }
        // If we are given just a message then assume it is an error message
        if (is_string($result)) {
            self::_setError($result, $name, $key);
            return true;
        }
        // If we are given an array with a key 'error' then extract the errors
        if (!empty($result['errors'])) {
            if (is_array($result['errors'])) {
                foreach($result['errors'] as $error) {
                    self::_setError($error, $name, $key);
                }
            } else {
                self::_setError($result['errors'], $name, $key);
            }
            return true;
        }
        // If we are given an array with a key 'warnings' then extract the warnings
        if (!empty($result['warnings'])) {
            if (is_array($result['warnings'])) {
                foreach($result['warnings'] as $warning) {
                    self::_setWarning($warning, $name, $key);
                }
            } else {
                self::_setWarning($result['warnings'], $name, $key);
            }
        }
        return false;
    }
    /**
     * This sets an error
     *
     * @return void
     * @param string $msg The error message to record
     * @param string $name[optional] The optional name of the module producing the error
     * @param string $key[optional] The optional name of the error
     */
    private static function _setError($msg, $name = '', $key = '')
    {
        if (empty($name)) $errors = & self::$errors;
        else $errors = & self::$errors[$name];
        if (empty($key)) $errors[] = __($msg);
        else $errors[$key] = __($msg);
        Kohana::log('error', 'INSTALL ERROR [' . $name . '][' . $key . ']: ' . $msg);
    }
    /**
     * This sets a warning
     *
     * @return void
     * @param string $msg The warnign message to record
     * @param string $name[optional] The optional name of the module producing the warning
     * @param string $key[optional] The optional name of the warning
     */
    private static function _setWarning($msg, $name = '', $key = '')
    {
        if (empty($name)) $warnings = & self::$warnings;
        else $warnings = & self::$warnings[$name];
        if (empty($key)) $warnings[] = __($msg);
        else $warnings[$key] = __($msg);
        Kohana::log('info', 'INSTALL WARNING [' . $name . '][' . $key . ']: ' . $msg);
    }
    /**
     * This is a place holder for a future method to check if
     * all a modules dependencies are avaliable post-install
     */
    private static function _verifyDependencyInstall($package)
    {
        /**
         * TODO: We need to do one finall check on default, all our depends need to
         * have be installed and enabled.
         */
        return TRUE;
    }
    /**
     * A simple wraper for version_compare so future version
     * requirements can be easily added (such as using regex)
     *
     * @return bool
     * @param string $requiredVersion PHP-standardized version number string to check against
     * @param string $avaliableVersion PHP-standardized version number strings to check
     * @param string $operator[optional] Test for a particular relationship. The possible operators are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne respectively
     */
    private static function _compareVersions($requiredVersion, $avaliableVersion, $operator = '>=')
    {
        $validOperators = array(
            '<',
            'lt',
            '<=',
            'le',
            '>',
            'gt',
            '>=',
            'ge',
            '==',
            '=',
            'eq',
            '!=',
            '<>',
            'ne'
        );
        // This might seem odd but inorder if the operator is in the version strings there has to be a space....
        if (strstr($requiredVersion, ' ') || strstr($avaliableVersion, ' ')) {
            // Check the strings for a valid operator
            foreach($validOperators as $validOperator) {
                $validOperator.= ' ';
                if (stristr($requiredVersion, $validOperator)) {
                    $requiredVersion = str_replace($validOperator, '', $requiredVersion);
                    $requiredVersion = (float)$requiredVersion;
                    $operator = str_replace(' ', '', $validOperator);
                    break;
                } else if (stristr($avaliableVersion, $validOperator)) {
                    $avaliableVersion = str_replace($validOperator, '', $avaliableVersion);
                    $avaliableVersion = (float)$avaliableVersion;
                    $operator = str_replace(' ', '', $validOperator);
                    break;
                }
            }
        }
        if (version_compare($avaliableVersion, $requiredVersion, $operator)) return true;
        else return false;
    }
    /**
     * This funtion finds and runs all _check methods in the configure class for packages with
     * the appropriate actions
     *
     * @params array $packages a list of packages to operate on
     */
    private static function _runCheckMethods($packages)
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'enable',
            'repair',
            'verify'
        );
        foreach($packages as $name => $package) {
            // If there is no action to preform move to the next package
            if (!in_array($package['action'], $runForActions)) continue;
            // Create a list of methods in this class that start with '_check' and can be executed
            if (!class_exists($package['configureClass'])) continue;
            $checkMethods = get_class_methods($package['configureClass']);
            $checkMethods = array_filter($checkMethods, array(
                'Bluebox_Installer',
                '_filterCheckMethods'
            ));
            if (empty($checkMethods) || !is_array($checkMethods)) continue;
            // For each method found run it and build a results array with the result
            foreach($checkMethods as $checkMethod) {
                // Call the function and get the returned result
                try {
                    $return = call_user_func(array(
                        $package['configureClass'],
                        $checkMethod
                    ));
                    // Track any error or warnings returned by the _function_check methods
                    self::_setIssue($return, $name);
                }
                catch(Exception $e) {
                    // This lets users set errors by throwing an exception
                    // It also catches any doctrine exceptions that the users _check may not....
                    self::_setError($e->getMessage() , $name);
                }
            }
        }
    }
    /**
     * This function is responsible for ensuring the requirements in the configure require array
     * can be met
     *
     * @param array a complete listing of avaliable packages that can be used to met the requirements
     * @param string the name of the package whoes requirements we need to check
     * @return array if a package can be installed but needs to do so disabled, the packages array is updated
     */
    private static function _fulfillRequired($packages, $name)
    {
        $runForActions = array(
            'downgrade',
            'install',
            'upgrade',
            'enable',
            'repair',
            'verify'
        );
        $package = $packages[$name];
        $requirements = $package['required'];
        // If this action does not rely on requirements return
        if (!in_array($package['action'], $runForActions)) return $packages;
        // If there are no requirements return
        if (empty($requirements) || !is_array($requirements)) return $packages;
        $relationTree = self::relationTree($packages);
        foreach($requirements as $required => $requiredVersion) {
            $requirement = strtolower($required);
            // check if the core mets requirements
            if ($requirement == 'core') {
                $coreVersion = Bluebox_Controller::$version;
                if (!self::_compareVersions($requiredVersion, $coreVersion)) self::_setError('This module is incompatable with Bluebox version ' . $coreVersion , $name);
                continue;
            }
            if (!empty($packages[$name]['incompatible'])) {
                unset($packages[$name]['incompatible']);
                continue;
            }
            // If this is a logic operation then handle it
            //$logicOperator = array('or', 'not', 'xor');
            $logicOperator = array(
                'not'
            );
            if (in_array($requirement, $logicOperator)) {
                if (!is_array($requiredVersion)) continue;
                // Push the array to the stack so we can hijack the errors array
                $errors = self::$errors;
                self::$errors = array();
                $tmpPackages = $packages;
                // First make our package look like it only requires this sub-array
                $tmpPackages[$name]['required'] = $requiredVersion;
                self::_fulfillRequired($tmpPackages, $name);
                // Save the new errors and return pop the error stack
                $subErrors = self::$errors;
                self::$errors = $errors;
                // This is counter-intuative but if the package is avaliable there are no errors....
                if ($requirement == 'not' && empty($subErrors)) {
                    foreach(array_keys($requiredVersion) as $subName) {
                        $incompatible = FALSE;
                        if (self::packageAvailable($packages, $subName) == 2) {
                            self::_setError('This module is incompatible with ' . $packages[$subName]['displayName'], $name);
                            self::_setError('This module is incompatible with ' . $packages[$name]['displayName'], $subName);
                            $packages[$subName]['incompatible'] = TRUE;
                            $incompatible = TRUE;
                        }
                        if (!empty($incompatible)) continue;
                    }
                } else if ($requirement == 'or') {
                    /** TODO: Do we even need this?  It will have to be addressed in everything that uses requirements **/
                } else if ($requirement == 'xor') {
                    /** TODO: Do we even need this?  It will have to be addressed in everything that uses requirements **/
                }
                unset($tmpPackages);
                continue;
            }
            // Required module can not be found
            if (empty($packages[$required])) {
                self::_setError('The required module ' . $required . ' could not be found', $name);
                continue;
            }
            $requirement = $packages[$required];
            // Should the version be retrieved from configure.php depending on its action, errors, and current status
            $requirementVersion = 0;
            $useConfigVersion = array(
                'downgrade',
                'install',
                'upgrade',
                'uninstall'
            );
            $configVersionValid = (in_array($requirement['action'], $useConfigVersion) && empty(self::$errors[$required]));
            // If the version should come from configure or isnt avaliable in the db get it!
            if ($configVersionValid || empty($requirement['installedAs']['module_version'])) {
                $requirementVersion = $requirement['version'];
            } else {
                $requirementVersion = $requirement['installedAs']['module_version'];
            }
            // Determine if the version is/could be satisfied
            $versionSatisfied = self::_compareVersions($requiredVersion, $requirementVersion);
            // The requirements status determines the behavoir
            switch (self::packageAvailable($packages, $required)) {
                // Case where a required module can not be found

            case -1:
                // This should have been caught above, but just incase....
                self::_setError('The required module ' . $required . ' could not be found', $name);
                break;
                // Case where a module can be found but is, and wont be, installed

            case 0:
                // If a required module is not installed but avaliable for installation then produce an error
                if ($versionSatisfied)
                // The required module is not installed but if it where things would be fine
                if (empty(self::$errors[$packages[$required]['packageName']])) {
                    self::_setError('The required module ' . $packages[$required]['displayName'] . ' exists but is not available', $name);
                } else {
                    kohana::log('error', 'SUPPRESSING RELIANCE ON ' . $packages[$required]['displayName'] . ' but it has an error');
                    //self::_setError('This package relies on ' .$packages[$required]['displayName'] . ' but it has an error', $name);
                }
                else
                // There is an uninstalled module by that name but it is the wrong version
                self::_setError('The required version of ' . $required . ' could not be found', $name);
                break;
                // Case where a module is, or will be, installed but disabled

            case 1:
                if (!$versionSatisfied) {
                    // The installed version does not met the requirement version
                    self::_setError('This module is incompatable with ' . $requirement['displayName'] . ' version ' . $requirementVersion , $name);
                } else if ($package['action'] == 'enable') {
                    // The requirement is disabled while this module tries to enable itself
                    self::_setError('This module requires ' . $requirement['displayName'] . ' to also be enabled', $name);
                } else if ($package['default']) {
                    // We cant enable this package because one if its dependencies is disabled so we will continue disabled as well
                    $packages[$name]['default'] = false;
                    self::_setWarning('The required module ' . $requirement['displayName'] . ' is disabled so this will default to disabled as well', $name);
                }
                break;
                // Case where a module is, or will be, installed

            case 2:
                if (!$versionSatisfied) {
                    // The installed version does not met the requirement version
                    self::_setError('This module is incompatable with ' . $requirement['displayName'] . ' version ' . $requirementVersion , $name);
                }
                break;
            }
        }
        return $packages;
    }
    /**
     * This function is responsible for ensuring the requirements in the configure require array
     * can be met
     *
     * @param array a complete listing of avaliable packages that can be used to met the requirements
     * @param string the name of the package whoes requirements we need to check
     * @return array if a package can be installed but needs to do so disabled, the packages array is updated
     */
    private static function _abandonRequired($packages, $name)
    {
        $runForActions = array(
            'disable',
            'uninstall'
        );
        $package = $packages[$name];
        $requirements = $package['required'];
        // If this action does not rely on requirements return
        if (!in_array($package['action'], $runForActions)) return $packages;
        // If there are no requirements return
        if (empty($requirements) || !is_array($requirements)) return $packages;
        $relationTree = self::relationTree($packages);
        foreach($relationTree[$name]['dependOf'] as $dependOf) {
            if (self::packageAvailable($packages, $dependOf) == 2) self::_setError('This module is used by ' . $packages[$dependOf]['displayName'] . ', so it must also be disabled', $name);
        }
        return $packages;
    }
    /**
     * This function returns true if an action is valid
     *
     * @return bool true if the action is valid, false otherwise
     * @param String the action to test against
     */
    private static function _isValidAction($action)
    {
        $validActions = array(
            'downgrade',
            'install',
            'upgrade',
            'enable',
            'disable',
            'uninstall',
            'repair',
            'verify'
        );
        return in_array($action, $validActions);
    }
    /**
     * This is the callback filters that list to only those methods
     * that start with '_check'.
     *
     * @return bool True if a method begins with '_check' otherwise false.
     * @param string $methodName The method name to test
     */
    private static function _filterCheckMethods($methodName)
    {
        return strstr($methodName, '_check');
    }
    /**
     * This is the callback filters that list to only those methods
     * that start with '_sample'.
     *
     * @return bool True if a method begins with '_sample' otherwise false.
     * @param string $methodName The method name to test
     */
    private static function _filterSampleMethods($methodName)
    {
        return strstr($methodName, '_sample');
    }

    /**
     * This function recursively handles a mixed message body
     * adding child tags where necessary.  Note: arrays with
     * numerical indexs will result in tags called {key}_{index}
     *
     * @param mixed $mixed The mixed element to operate on
     * @param string $key The name to use for any non-associative array child tags
     * @param object $parent the parent tag
     * @return void
     */
    private static function _xmlChildMixed($mixed, $key, &$parent) {
        if (is_array($mixed)) {
            foreach ($mixed as $k => $v) {

                if (is_int($k)) {
                    $k = $key .'_' .$k;
                }

                $k = trim(preg_replace('/[^a-zA-Z_]+/imx', '_', $k), '_');
                $k = strtolower($k);

                $state = is_array($v);
                $state <<= 1;
                $state |= empty($parent->$k);

                // is_array | empty

                switch ($state) {
                    // not an array but parent is not empty
                    case 0:
                        $parent->$k = $v;
                        break;

                    // not an array and parent is emtpy
                    case 1:
                        $parent->addChild($k, $v);
                        break;

                    // is an array but parent is not empty
                    case 2:
                        self::_xmlChildMixed($v, $k, $parent->$k);
                        break;

                    // is an array and parent is empty
                    case 3:
                        self::_xmlChildMixed($v, $k, $parent->addChild($k));
                        break;
                }
            }
        } else if (empty($parent->$key)) {
            $key = trim(preg_replace('/[^a-zA-Z_]+/imx', '_', $key), '_');
            $key = strtolower($k);
            $parent->addChild($key, $mixed);
        } else {
            $parent->$key = $mixed;
        }
    }
    /**
     * Request the headers of the remote url and return the modified time.  This
     * is used to determine if a repository has a newer version catalog then the
     * one we last cached.  Based on random googling....
     *
     * @param string $url
     * @return int
     */
    private static function _getRemoteLastModified( $url ) {
        $portno = 80;
        $method = "HEAD";

        $http_response = "";
        $http_request  = $method." ".$url ." HTTP/1.0\r\n";
        $http_request .= "\r\n";

        kohana::log('debug', 'Checking ' .$url);

        $fp = @fsockopen(parse_url($url, PHP_URL_HOST), $portno, $errno, $errstr, 10);
        if($fp){

            fputs($fp, $http_request);

            while (!feof($fp)) {
                $http_response .= fgets($fp, 128);
            }

            $info = stream_get_meta_data($fp);

            fclose($fp);

            if ($info['timed_out']) {
                kohana::log('error', 'Could not determine the remote file mod time: Connection timed out');
                return 0;
            }
        } else {
            kohana::log('error', 'Could not determine the remote file mod time: ' .$errstr);
            return 0;
        }

        $http_response = explode("\r\n", $http_response);

        foreach( $http_response as $response )
        {
            if( substr( strtolower($response), 0, 10 ) == 'location: ' )
            {
                $newUri = substr( $response, 10 );
                kohana::log('alert', 'Request was redirected to: ' .$newUri);
                return self::_getRemoteLastModified( $newUri );
            } elseif( substr( strtolower($response), 0, 15 ) == 'last-modified: ' ) {
                $unixtime = strtotime( substr($response, 15) );
                break;
            }
        }

        kohana::log('debug', 'Last modified at ' .$unixtime);

        return $unixtime;
    }
    public function installCore(&$packages) {
        $core['core'] = array (
            'version' => Bluebox_Controller::$version,
            'packageName' => 'core',
            'author' => 'Bluebox Team',
            'vendor' => 'Bluebox',
            'license' => 'MPL',
            'summary' => 'Core Application',
            'description' => '',
            'default' => TRUE,
            'type' => Bluebox_Installer::TYPE_MODULE,
            'required' => array (),
            'displayName' => 'System Core',
            'canBeDisabled' => FALSE,
            'canBeRemoved' => FALSE,
            'directory' => 'bluebox',
            'configureClass' => '',
            'installedAs' => array (
                'module_id' => '0',
                'name' => 'core',
                'display_name' => 'System Core',
                'module_version' => Bluebox_Controller::$version,
                'enabled' => TRUE,
                'basedir' => 'bluebox',
                'parameters' => array (
                    'version' => Bluebox_Controller::$version,
                    'packageName' => 'core',
                    'author' => 'Bluebox Team',
                    'vendor' => 'Bluebox',
                    'license' => 'MPL',
                    'summary' => 'Core Application',
                    'description' => '',
                    'default' => TRUE,
                    'type' => Bluebox_Installer::TYPE_MODULE,
                    'required' => array(),
                    'displayName' => 'System Core',
                    'canBeDisabled' => FALSE,
                    'canBeRemoved' => FALSE,
                    'directory' => 'bluebox',
                    'configureClass' => '',
                    'navStructures' => array()
                )
            ),
            'models' => array(),
            'created_at' => '2010-03-27 08:29:06',
            'updated_at' => '2010-03-27 08:53:43',
            'action' => FALSE,
            'navStructures' => array()
        );
        $packages = arr::merge($core, $packages);
    }
}
