<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 INSTALLS     UPGRADES     DOWNGRADES     REPAIR       ENABLE       DISABLE       UNINSTALL
 --------------------------------------------------------------------------------------------
 verify	      verify       verify         verify       verify
 preinstall   preupgrade   predowngrade                                          preuninstall
 install      upgrade      downgrade      repair       enable       disable      uninstall
 postinstall  postupgrade  postdowngrade                                         postuninstall
 */

class Bluebox_PackageManager
{
    const STATUS_UNACCESSIBLE = 'unaccessible';
    const STATUS_UNINSTALLED = 'uninstalled';
    const STATUS_DISABLED = 'disabled';
    const STATUS_INSTALLED = 'installed';

    const OPERATION_DOWNGRADE = 'downgrade';
    const OPERATION_INSTALL = 'install';
    const OPERATION_UPGRADE = 'upgrade';
    const OPERATION_ENABLE = 'enable';
    const OPERATION_DISABLE = 'disable';
    const OPERATION_REPAIR = 'repair';
    const OPERATION_VERIFY = 'verify';
    const OPERATION_UNINSTALL = 'uninstall';

    const TYPE_DEFAULT = 'unknown';
    const TYPE_CORE = 'core';
    const TYPE_MODULE = 'module';
    const TYPE_PLUGIN = 'plugin';
    const TYPE_DRIVER = 'driver';
    const TYPE_SERVICE = 'service';
    const TYPE_DIALPLAN = 'dialplan';
    const TYPE_ENDPOINT = 'endpoint';
    const TYPE_SKIN = 'skin';

    protected $configureCache = array();

    protected $catalog = array();

    protected $transaction = array();

    private static $instance = NULL;

    private function __construct()
    {

    }

    /**
     * This implements a singleton desing patterns ensureing all
     * packageManager operations occure on the same instance.  If a
     * catalog is provided then load the catalog into memory.
     * Otherwise build the catalog if we are being called for the
     * first time
     *
     * @param array A valid packageManager catalog of the system packages
     * @return Core_PackageManager
     */
    public static function instance($catalog = NULL)
    {
        if (!isset(self::$instance))
        {
            $class = __CLASS__;

            self::$instance = new $class;

            if (is_null($catalog))
            {
                self::$instance->createCatalog();
            }
        }

        if (!is_null($catalog))
        {
            self::$instance->catalog = $catalog;
        }

        return self::$instance;
    }

    /**
     * Reset a pending change to a package or all changes
     *
     * @param mixed If provided as a package name or array of names just chanages
     *              to those packages will be reset. Otherwise reset all changes
     */
    public function reset($packageList = NULL)
    {
        if (is_null($packageList))
        {
            $this->transaction = array();
            
            return;
        }

        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        foreach($packageList as $packageName)
        {
            if (array_key_exists($packageName, $this->transaction))
            {
                unset($this->transaction[$packageName]);
            } 
            else
            {
                //TODO: this breaks the concept of $this->transaction... hmmm
                throw new PackageManager_Exception('Package "' .$packageName .'" has not been modified');
            }
        }
    }

    /**
     * Find and standardize all configure.php files in the system. This becomes
     * a list of packages, the package meta data, and the package status from
     * which all other functions operate.
     */
    public function createCatalog()
    {
        $catalog = array();

        $dbMappings = array();
        
        $dbCatalog = Doctrine::getTable('Package')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($dbCatalog as $dbPackage)
        {
            $dbMappings[$dbPackage['name']] = $dbPackage;
        }

        // Build an array of all the configure.php files on the system.
        $configureFiles = glob(MODPATH . '*/configure.php', GLOB_MARK);

        // Run through all the configure.php files and include them
        foreach($configureFiles as $configureFile)
        {
            // Add what we think might be a configure.php file
            require_once ($configureFile);

            // Get the last added class
            $declaredAfter = get_declared_classes();

            $foundClass = end($declaredAfter);

            // Check if we found a Core_Configure class
            if ($foundClass && is_subclass_of($foundClass, 'Bluebox_Configure'))
            {
                // If we have found a configure class add it to the list
                $this->configureCache[$foundClass] = $configureFile;
            }
        }
        
        // Process each of the known package configuration classes
        foreach($this->configureCache as $class => $configureFile)
        {
            // Get a list of all the static vars of this package
            $packageVars = get_class_vars($class);

            // get the directory of the package
            $packageVars['directory'] = dirname($configureFile);

            if (empty($packageVars['packageName']))
            {
                $packageVars['packageName'] = dirname(str_replace(MODPATH, '', $configureFile));
            }
            
            // If the moduleName is empty use the class name
            //if (empty($packageVars['packageName'])) $packageVars['packageName'] = str_replace('_Configure', '', $class);
            // moduleName is used in a lot of places, and I am lazy ;)
            $packageName = $packageVars['packageName'];

            // If this package is already been loaded into the catalog skip the rest of this
            if (array_key_exists($packageName, $catalog))
            {
                continue;
            }

            // If there is no displayName specified then attempt to make one from the packageName
            if (empty($packageVars['displayName']))
            {
                $packageVars['displayName'] = ucfirst(inflector::humanize($packageName));
            }

            // make sure the default value is a bool ...
            if (!is_bool($packageVars['default']))
            {
                // ... if not consider it FALSE
                $packageVars['default'] = FALSE;
            }

            // verify the type and preform an standardization based on that type
            switch($packageVars['type'])
            {
                case self::TYPE_DEFAULT:
                    kohana::log('alert', 'Package ' . $packageName . ' is using the default package type');

                    break;

                case self::TYPE_CORE:
                    $packageVars['default'] = TRUE;

                    $packageVars['denyDisable'] = TRUE;

                    $packageVars['denyRemoval'] = TRUE;

                    break;

                case self::TYPE_MODULE:
                case self::TYPE_PLUGIN:
                case self::TYPE_DRIVER:
                case self::TYPE_SERVICE:
                case self::TYPE_DIALPLAN:
                case self::TYPE_ENDPOINT:
                case self::TYPE_SKIN:
                    break;

                default:
                    $packageVars['type'] = self::TYPE_DEFAULT;

                    kohana::log('error', 'Package ' . $packageName . ' is using an invalid package type, set to default');
            }

            // Standardize the required list as an array
            if (!is_array($packageVars['required']))
            {
                $packageVars['required'] = array();
                
                kohana::log('error', 'Package ' . $packageName . ' required parameter is poorly formated, ignoring');
            }

            // Save the name of the configuration class
            $packageVars['configureClass'] = $class;

            // Default packageStatus (set later)
            $packageVars['packageStatus'] = self::STATUS_UNINSTALLED;

            // Default database id (set later)
            $packageVars['databaseID'] = NULL;

            // if the navStructures array is missing or not an array build it from the individual values
            if (!isset($packageVars['navStructures']) || !is_array($packageVars['navStructures']))
            {
                if (!is_null($packageVars['navURL']))
                {
                    $packageVars['navStructures'] = array(array_intersect_key(
                        $packageVars,
                        array_flip(array('navBranch', 'navURL', 'navLabel', 'navSummary', 'navSubmenu'))
                    ));
                } 
                else if ($packageVars['type'] == self::TYPE_MODULE)
                {
                     kohana::log('error', 'Package ' . $packageName . ' of type module does not have any valid navigation defined');
                }
            }
            
            // if the navStructures is an array make sure it is in the correct format
            if (isset($packageVars['navStructures']))
            {
                if (!array_key_exists(0, $packageVars['navStructures']))
                {
                    $packageVars['navStructures'] = array($packageVars['navStructures']);
                }

                foreach ($packageVars['navStructures'] as $key => $navStructure)
                {
                    // each navigation structure must have the base url defined
                    if (empty($navStructure['navURL']))
                    {
                        kohana::log('error', 'Package ' . $packageName . ' has defined invalid navigation, ignoring');

                        unset($packageVars['navStructures'][$key]);
                        
                        continue;
                    }
                    
                    // if the navigation structure does not have a lable use the package display name
                    if (empty($navStructure['navLabel']))
                    {
                        $packageVars['navStructures'][$key]['navLabel'] = $packageVars['displayName'];
                    }

                    // if the navigation structure does not have a summary use the package summary
                    if (empty($navStructure['navSummary']))
                    {
                        $packageVars['navStructures'][$key]['navSummary'] = $packageVars['summary'];
                    }

                    // if the navigation structure doesn not define a branch then assume 'root'
                    if (empty($navStructure['navBranch']))
                    {
                        $packageVars['navStructures'][$key]['navBranch'] = '/';
                    }

                    // if the navigation structure does not have a submenu or that submenu
                    // is not an array then defualt to an empty array.  Otherwise check the submenu
                    if (!isset($navStructure['navSubmenu']))
                    {
                        $packageVars['navStructures'][$key]['navSubmenu'] = array();
                    } 
                    else if (!is_array($navStructure['navSubmenu']))
                    {
                        kohana::log('error', 'Package ' . $packageName . ' defined an invalid submenu!');

                        $packageVars['navStructures'][$key]['navSubmenu'] = array();
                    } 
                    else
                    {
                        $submenuItems = array();

                        foreach ($navStructure['navSubmenu'] as $name => $submenu)
                        {
                            if (is_string($submenu))
                            {
                                $submenu = array ('url' => $submenu);
                            }

                            if (empty($submenu['url']))
                            {
                                kohana::log('error', 'Package ' . $packageName . ' defined an invalid submenu item ' .$name);
                                
                                continue;
                            } 
                            else
                            {
                                $submenuItem = &$submenuItems[$name];
                                
                                $submenuItem['url'] = $submenu['url'];
                            }

                            if (empty($submenu['disabled']))
                            {
                                $submenuItem['disabled'] = FALSE;
                            } 
                            else
                            {
                                $submenuItem['disabled'] = TRUE;
                            }

                            if (trim($submenuItem['url'], '/') == trim($navStructure['navURL'], '/'))
                            {
                                $submenuItem['entry'] = TRUE;
                            } 
                            else
                            {
                                $submenuItem['entry'] = FALSE;
                            }
                        }

                        $packageVars['navStructures'][$key]['navSubmenu'] = $submenuItems;
                    }
                }
            } 
            else
            {
                $packageVars['navStructures'] = array();
            }

            // remove the unecessray configure variables 
            unset(
                $packageVars['navBranch'],
                $packageVars['navURL'],
                $packageVars['navLabel'],
                $packageVars['navSummary'],
                $packageVars['navSubmenu']
            );

            $packageVars['instance'] = NULL;

            if (class_exists($packageVars['configureClass']))
            {
                $packageVars['instance'] = new $packageVars['configureClass'];
            }

            // Get a directory listing of the php files in models for this package
            $possibleModels = glob($packageVars['directory'] . '/models/*.php', GLOB_MARK);

            // Attempt to load any models that belong to this module
            if (!empty($possibleModels))
            {
                $packageVars['models'] = Doctrine::loadModels($packageVars['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
            } 
            else
            {
                $packageVars['models'] = array();
            }

            // If we got to this point save the vars
            $catalog[$packageName] = $packageVars;

            // If this package does not have an entry in the db then skip to the next
            if (empty($dbMappings[$packageName]))
            {
                continue;
            }

            // Get the parameters retrieved from the db for this package
            $dbMapping = &$dbMappings[$packageName];

            // update the package db id
            $catalog[$packageName]['databaseID'] = $dbMapping['package_id'];

            // update the package status (if it is valid)
            switch($dbMapping['status'])
            {
                case self::STATUS_UNACCESSIBLE:
                    // This should probably be packages in db that are not
                    // on the filesystem so they can be uninstalled?
                    break;

                case self::STATUS_UNINSTALLED:
                    $catalog[$packageName]['packageStatus'] = self::STATUS_UNINSTALLED;

                    break;

                case self::STATUS_DISABLED:
                    $catalog[$packageName]['packageStatus'] = self::STATUS_DISABLED;

                    break;

                case self::STATUS_INSTALLED:
                    $catalog[$packageName]['packageStatus'] = self::STATUS_INSTALLED;

                    break;
            }
        }

        $this->catalog = $catalog;
    }

    /**
     * Returns the catalog array after applying the optional filter
     *
     * @param mixed An optional fiter of include only or exclude package types
     * @return array
     */
    public function getCatalog($filter = array())
    {
        $catalog = array();
        
        // Check if there are any filters
        if (!empty($filter))
        {
            // If we are only supplied with a string then assume it is a inlcude filter
            if (is_string($filter))
            {
                $filter = array('include' => array($filter));
            } 
            else if (is_array($filter) && empty($filter['include']) && empty($filter['exclude']))
            {
                // If we are given a one dimentional array then assume it is also an include filter
                $filter = array('include' => $filter);
            }

            // Ensure sub-keys are arrays themselfs
            if (!empty($filter['include']) && is_string($filter['include']))
            {
                $filter['include'] = array( $filter['include']);
            }

            if (!empty($filter['exclude']) && is_string($filter['exclude']))
            {
                $filter['exclude'] = array($filter['exclude']);
            }
        }
        
        if (!is_array($filter))
        {
            $filter = array();
        }

        // Merge the corrected filters array with the defaults to fill in any blanks
        $filter = array_merge(array(
            'include' => array() ,
            'exclude' => array()
        ) , $filter);

        foreach ($this->catalog as $packageName => $package)
        {
            // Check if there are any exlcude filters and apply them
            if (!empty($filter['exclude']) && in_array($package['type'], $filter['exclude']))
            {
                continue;
            }

            // Check if there are any include filters and apply them
            if (!empty($filter['include']) && !in_array($package['type'], $filter['include'])) 
            {
                continue;
            }
            
            $catalog[$packageName] = $package;
        }

        return $catalog;
    }

    /**
     * Sets the packages provided in the first parameter to be downgraded.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function downgrade($packageNames = array())
    {
        throw new PackageManager_Exception('This feature is not yet implemented');
    }

    /**
     * Sets the packages provided in the first parameter to be installed.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function install($packageList = array())
    {
        // make sure we are always dealing with an array of packages to install
        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        $transaction = array();

        foreach($packageList as $packageName)
        {
            $catalog = $this->catalog;

            if (!array_key_exists($packageName, $catalog))
            {
                throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
            }
            
            $package = $catalog[$packageName];

            $package['operation'] = self::OPERATION_INSTALL;
            
            $transaction[$packageName] = $package;
        }
        
        $this->transaction = arr::merge($this->transaction, $transaction);
        
        return $this->validate();
    }

    /**
     * Sets the packages provided in the first parameter to be upgraded.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function upgrade($packageList = array())
    {
        throw new PackageManager_Exception('This feature is not yet implemented');
    }

    /**
     * Sets the packages provided in the first parameter to be enabled.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function enable($packageList = array())
    {
        // make sure we are always dealing with an array of packages to install
        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        $transaction = array();

        foreach($packageList as $packageName)
        {
            $catalog = $this->catalog;

            if (!array_key_exists($packageName, $catalog))
            {
                throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
            }

            $package = $catalog[$packageName];

            $package['operation'] = self::OPERATION_ENABLE;

            $transaction[$packageName] = $package;
        }

        $this->transaction = arr::merge($this->transaction, $transaction);

        return $this->validate();
    }

    /**
     * Sets the packages provided in the first parameter to be disabled.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function disable($packageList = array())
    {
        // make sure we are always dealing with an array of packages to install
        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        $transaction = array();

        foreach($packageList as $packageName)
        {
            $catalog = $this->catalog;

            if (!array_key_exists($packageName, $catalog))
            {
                throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
            }

            $package = $catalog[$packageName];

            $package['operation'] = self::OPERATION_DISABLE;
            
            $transaction[$packageName] = $package;
        }

        $this->transaction = arr::merge($this->transaction, $transaction);

        return $this->validate();
    }

    /**
     * Sets the packages provided in the first parameter to be repaired.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function repair($packageList = array())
    {
        // make sure we are always dealing with an array of packages to install
        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        $transaction = array();

        foreach($packageList as $packageName)
        {
            $catalog = $this->catalog;

            if (!array_key_exists($packageName, $catalog))
            {
                throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
            }

            $package = $catalog[$packageName];

            $package['operation'] = self::OPERATION_REPAIR;
            
            $transaction[$packageName] = $package;
        }

        $this->transaction = arr::merge($this->transaction, $transaction);

        return $this->validate();
    }

    /**
     * Sets the packages provided in the first parameter to be unistalled.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function uninstall($packageList = array())
    {
        // make sure we are always dealing with an array of packages to install
        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        $transaction = array();

        foreach($packageList as $packageName)
        {
            $catalog = $this->catalog;

            if (!array_key_exists($packageName, $catalog))
            {
                throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
            }

            $package = $catalog[$packageName];

            $package['operation'] = self::OPERATION_UNINSTALL;
            
            $transaction[$packageName] = $package;
        }

        $this->transaction = arr::merge($this->transaction, $transaction);

        return $this->validate();
    }

    /**
     * Verifies the packages provided as the first parameter or transaction.  Unlike
     * the other operations this returns the final result (does not impact the
     * transaction).  If packageList is not provided it verifies the transaction.
     *
     * @param mixed A string package name or array of package names as values
     * @return array
     */
    public function verify($packageList = array())
    {
        // make sure we are always dealing with an array of packages to install
        if(!is_array($packageList))
        {
            $packageList = array($packageList);
        }

        $transaction = array();

        foreach($packageList as $packageName)
        {
            $catalog = $this->catalog;

            if (!array_key_exists($packageName, $catalog))
            {
                throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
            }

            $package = $catalog[$packageName];

            $package['operation'] = self::OPERATION_VERIFY;
            
            $transaction[$packageName] = $package;
        }

        // and now for something completely different......
        $transactionCache = $this->transaction;

        $this->transaction = arr::merge($this->transaction, $transaction);

        // validate the dependencies of this transaction
        $results = $this->validate();

        // set up some pointers to our return
        $error = &$results['error'];

        $warning = &$results['warning'];

        $notice = &$results['notice'];

        $ok = &$results['ok'];

        // becuase we need to run the verify even if the dependencies are not
        // met at this stage we run it here
        foreach ($this->transaction as $packageName => $package)
        {
            if ($package['type'] == self::OPERATION_VERIFY)
            {
                continue;
            }

            if (empty($package['instance']) || !is_object($package['instance']))
            {
                continue;
            }

            try
            {
                $stepResult = call_user_func(array($package['instance'], 'verify'));

                if ($stepResult === FALSE)
                {
                    $error[$packageName][] =
                        'Unspecified error occured in step "verify" for ' .$this->displayName($packageName);
                } 
                else if (is_string($stepResult))
                {
                    $error[$packageName][] = $stepResult;

                    unset($stepResult);
                }

                if (isset($stepResult['error']))
                {
                    $error[$packageName] = array_merge_recursive(
                        (array)$error[$packageName],
                        (array)$stepResult['error']
                    );
                }

                if (isset($stepResult['warning']))
                {
                    $results['warning'][$packageName] = array_merge_recursive(
                        (array)$warning[$packageName],
                        (array)$stepResult['warning']
                    );
                }

                if (isset($stepResult['notice']))
                {
                    $results['notice'][$packageName] = array_merge_recursive(
                        (array)$notice[$packageName],
                        (array)$stepResult['notice']
                    );
                }

            } 
            catch(Exception $e)
            {
                $error[$packageName][] = $e->getMessage();
            }
        }

        // it is possible that a package passed validation by failed verfication
        // so remove any 'ok' message for such cases
        if (is_array($ok))
        {
            foreach ($ok as $packageName => $message)
            {
                if (!empty($error[$packageName]) || !empty($warning[$packageName]))
                {
                    unset($ok[$packageName]);
                }
            }
        }

        // we now return you to your normally schedualed program already in progress...
        $this->transaction = $transactionCache;
        
        return $results;
    }

    /**
     * Executes any pending package changes
     */
    public function commit()
    {
        $results = $this->validate();

        if (!empty($results['error']) || !empty($results['warning']))
        {
            throw new PackageManager_Dependency_Exception('The transaction did not validate');
        }

        $operations = array(
            self::OPERATION_UNINSTALL,
            self::OPERATION_DISABLE,
            self::OPERATION_VERIFY,
            self::OPERATION_DOWNGRADE,
            self::OPERATION_INSTALL,
            self::OPERATION_UPGRADE,
            self::OPERATION_ENABLE,
            self::OPERATION_REPAIR
        );

        $transactions = $this->transaction;

        $this->reset();

        $results = array();

        foreach ($operations as $operation)
        {
            $transaction = array();
            
            foreach ($transactions as $packageName => $package)
            {
                if ($package['operation'] != $operation)
                {
                    continue;
                }

                $transaction[$packageName] = $package;
            }

            if (empty($transaction))
            {
                continue;
            }

            $this->doOperation($transaction, $operation, $results);
        }

        Bluebox_Core::bootstrapPackages();

        $this->reset();

        return $results;
    }

    protected function doOperation($transaction, $operation, &$results) {
        // set up some pointers to our return
        $error = &$results['error'];
        $warning = &$results['warning'];
        $notice = &$results['notice'];
        $ok = &$results['ok'];
        
        $steps = array();
        switch($operation) {
            case self::OPERATION_VERIFY:
                $steps = array('verify');
                break;

            case self::OPERATION_DOWNGRADE:
                $steps = array('verify', 'preDowngrade', 'downgrade', 'postDowngrade');
                break;

            case self::OPERATION_INSTALL:
                $steps = array('verify', 'preInstall', 'install', 'postInstall');
                break;

            case self::OPERATION_UPGRADE:
                $steps = array('verify', 'preUpdate', 'update', 'postUpdate');
                break;

            case self::OPERATION_UNINSTALL:
                $steps = array('preUninstall', 'uninstall', 'postUninstall');
                break;

            case self::OPERATION_DISABLE:
                $steps = array('disable');
                break;

            case self::OPERATION_ENABLE:
                $steps = array('verify', 'enable');
                break;

            case self::OPERATION_REPAIR:
                $steps = array('verify', 'repair');
                break;
        }

        foreach ($steps as $step) {
            foreach ($transaction as $packageName => $package) {
                // If this is a downgrade, install, or update step then
                // ensure that this module is in the konana config
                switch ($step) {
                    case 'downgrade':
                    case 'install':
                    case 'update':
                        if (empty($package['directory'])) continue;

                        $loadedModules = Kohana::config('core.modules');
                        $systemModules = array_unique(array_merge($loadedModules, array($packageName => $package['directory'])));
                        Kohana::config_set('core.modules', $systemModules);

                        break;
                }

                if (empty($package['instance']) || !is_object($package['instance'])) continue;

                try {

                    $stepResult = call_user_func_array(array($package['instance'], $step), array($package));

                    if ($stepResult === FALSE) {
                        $error[$packageName][] =
                            'Unspecified error occured in step "' .$step .'" for ' .$this->displayName($packageName);
                    } else if (is_string($stepResult)) {
                        $error[$packageName][] = $stepResult;
                        unset($stepResult);
                    }

                    if (isset($stepResult['error'])) {
                        $error[$packageName] = array_merge_recursive(
                            (array)$error[$packageName],
                            (array)$stepResult['error']
                        );
                    } 
                    
                    if (isset($stepResult['warning'])) {
                        $warning[$packageName] = array_merge_recursive(
                            (array)$warning[$packageName],
                            (array)$stepResult['warning']
                        );
                    }

                    if (isset($stepResult['notice'])) {
                        $notice[$packageName] = array_merge_recursive(
                            (array)$notice[$packageName],
                            (array)$stepResult['notice']
                        );
                    }
                    
                } catch(Exception $e) {
                    $error[$packageName][] = $e->getMessage();
                }

                // if this step was in error then handle it
                if (!empty($error[$packageName]) || !empty($warning[$packageName])) {
                    // rollback any changes (if and when possible)
                    // Remove the package from the remaining operations
                    unset($transaction[$packageName]);
                }
            }
        }

        foreach($transaction as $packageName => $package) {
            if (!empty($error[$packageName]) || !empty($warning[$packageName])) {
                continue;
            }

            $ok[$packageName][] =
                ucfirst(self::operationToString($package['operation']))
                .' succeeded';

            switch($package['operation']) {

                case self::OPERATION_UNINSTALL:
                    $this->updatePackage($packageName, 'packageStatus', self::STATUS_UNINSTALLED);
                    $this->removeNumberType($package['models']);
                    break;

                case self::OPERATION_DISABLE:
                    $this->updatePackage($packageName, 'packageStatus', self::STATUS_DISABLED);
                    break;

                case self::OPERATION_ENABLE:
                    $this->updatePackage($packageName, 'packageStatus', self::STATUS_INSTALLED);
                    $this->integrateNumberType($package['models'], $this->catalog[$packageName]['databaseID']);
                    break;

                case self::OPERATION_DOWNGRADE:
                case self::OPERATION_UPGRADE:
                    $this->integrateNumberType($package['models'], $this->catalog[$packageName]['databaseID']);
                    // This status needs to be whatever the previous package was
                    break;
                
                case self::OPERATION_INSTALL:
                    $this->updatePackage($packageName, 'packageStatus', self::STATUS_DISABLED);
                    if ($this->catalog[$packageName]['default'] === TRUE) {
                        $this->enable($packageName);
                        $this->integrateNumberType($package['models'], $this->catalog[$packageName]['databaseID']);
                    }
                    break;
                case self::OPERATION_REPAIR:
                    $this->updatePackage($packageName, 'packageStatus', $this->catalog[$packageName]['packageStatus']);
                    $this->integrateNumberType($package['models'], $this->catalog[$packageName]['databaseID']);
                    break;
            }
        }

        if (!empty($this->transaction)) {
            $validation = $this->validate();

            if (!empty($validation['error'])) {
                foreach ($validation['error'] as $packageName => $installError) {
                    $warning[$packageName][] =
                        'This package installed but could not be enabled';
                    try {
                        $this->reset($packageName);
                    } catch(PackageManager_Exception $e) {}
                }
            }

            if (!empty($validation['warning'])) {
                foreach ($validation['warning'] as $packageName => $installWarning) {
                    $warning[$packageName][] =
                        'This package installed but could not be enabled';
                    try {
                        $this->reset($packageName);
                    } catch(PackageManager_Exception $e) {}
                }
            }
            
            $this->commit();
        }
    }

    public function updatePackage($packageName, $key, $value) {
        try {
            $package = &$this->getPackage($packageName, FALSE);
        } catch (PackageManager_Catalog_Exception $e) {
            return FALSE;
        }

        if (empty($package['databaseID'])) {
            $dbPackage = new Package();
        } else {
            $dbPackage = Doctrine::getTable('Package')->find($package['databaseID']);
        }

        $package[$key] = $value;

        $registryIgnoreKeys = array_flip(array(
            'packageName',
            'displayName',
            'version',
            'packageStatus',
            'directory',
            'instance',
            'navStructures',
            'databaseID',
            'type'
        ));

        $dbPackage['name'] = $package['packageName'];
        $dbPackage['display_name'] = $package['displayName'];
        $dbPackage['version'] = $package['version'];
        $dbPackage['type'] = $package['type'];
        $dbPackage['status'] = $package['packageStatus'];
        $dbPackage['basedir'] = str_replace(DOCROOT, '', $package['directory']);
        $dbPackage['navigation'] = $package['navStructures'];
        $dbPackage['registry'] = array_diff_key($package, $registryIgnoreKeys);
        $dbPackage->save();

        $package['databaseID'] = $dbPackage['package_id'];

        return TRUE;
    }
    
    /**
     * Ensures the sanity of any pending package changes
     *
     * @return array
     */
    public function validate(&$results = array()) {
        // set up some pointers to our return
        $error = &$results['error'];
        $warning = &$results['warning'];
        $notice = &$results['notice'];
        $ok = &$results['ok'];

        // Validate that the operation makes sense
        foreach ($this->transaction as $packageName => $package) {
            switch($package['packageStatus']) {

                case self::STATUS_UNINSTALLED:
                    switch ($package['operation']){
                        case self::OPERATION_VERIFY:
                        case self::OPERATION_INSTALL:
                            continue 3;
                    }
                    break;

                case self::STATUS_DISABLED:
                    switch ($package['operation']){
                        case self::OPERATION_VERIFY:
                        case self::OPERATION_ENABLE:
                        case self::OPERATION_DOWNGRADE:
                        case self::OPERATION_UPGRADE:
                        case self::OPERATION_REPAIR:
                        case self::OPERATION_UNINSTALL:
                            continue 3;
                    }
                    break;

                case self::STATUS_INSTALLED:
                    switch ($package['operation']){
                        case self::OPERATION_VERIFY:
                        case self::OPERATION_DISABLE:
                        case self::OPERATION_DOWNGRADE:
                        case self::OPERATION_UPGRADE:
                        case self::OPERATION_REPAIR:
                        case self::OPERATION_UNINSTALL:
                            continue 3;
                    }
                    break;

                case self::STATUS_UNACCESSIBLE:
                    break;

                default:
                    throw new PackageManager_Exception('Package "' .$this->displayName($packageName) .'" in unknown state: ' .$package['packageStatus']);
            }
            
            $error[$packageName][] =
                ucfirst(self::operationToString($package['operation']))
                .' is a illegal operation for a '
                .self::statusToString($package['packageStatus'])
                .' package';
        }
        
        // run check methods
        $this->runPackageChecks($results);

        // Check that all packages that will become part of the system
        // met all their requirements and dont conflict
        $this->validateIntegration($results);

        // Checck that all packages being removed from the running system
        // are not relied on by other packages
        $this->validateAbandon($results);
        
        return $results;
    }

    /**
     * Ensure that the packages in the transaction that we will be abandoning
     * from the system (ie: disable, uninstall) will not cause/have problems
     *
     * @param array The shared messages array for the validate functions
     */
    public function validateAbandon(&$results) {
        // set up some pointers to our return
        $error = &$results['error'];
        $warning = &$results['warning'];
        $notice = &$results['notice'];
        $ok = &$results['ok'];

        // reverse the required directed graph
        $relianceGraph = array();
        foreach ($this->catalog as $packageName => $package) {
            if (!array_key_exists($packageName, $relianceGraph)) {
                $relianceGraph[$packageName] = array();
            }

            $required = $package['required'];
            if (!is_array($required)) continue;

            foreach($required as $requiredPackage => $version) {

                if (!array_key_exists($requiredPackage, $relianceGraph)) {
                    $relianceGraph[$requiredPackage] = array();
                }

                $relianceGraph[$requiredPackage][] = $packageName;
            }
        }

        // create a list of all the packages that are reliant on packages
        //  in the transaction (in order)
        $reliantPackages = array();
        foreach ($this->transaction as $packageName => $package) {
            switch($package['operation']) {

                case self::OPERATION_DISABLE:
                    if (!empty($package['denyDisable'])) {
                        $error[$packageName][] = 'This package can never be disabled';
                        break;
                    }
                    
                case self::OPERATION_UNINSTALL:
                    if (!empty($package['denyRemoval'])) {
                        $error[$packageName][] = 'This package can never be uninstalled';
                        break;
                    }
                    try {
                        $dependencies = $this->graphReliance($relianceGraph, $packageName);
                        $dependencies = array_flip($dependencies);
                        foreach ($dependencies as $key => $value) {
                            $dependencies[$key] = array($packageName);
                        }
                        $reliantPackages = array_merge_recursive($dependencies, $reliantPackages);
                    } catch(PackageManager_Catalog_Exception $e) {
                        $error[$packageName][] = $e->getMessage();
                        continue;
                    } catch(PackageManager_Dependency_Exception $e) {
                        $error[$packageName][] = $e->getMessage();
                        continue;
                    }
                    break;
            }
        }

        // enforce the reliance
        foreach ($reliantPackages as $reliantPackage => $dependents) {
            switch($this->packageStatus($reliantPackage)) {
                // if this package is relied on by other installed (enabled) packages
                // then we can are still needed by they system!
                case self::STATUS_INSTALLED:
                    foreach($dependents as $dependent) {
                        if ($dependent == $reliantPackage) continue;
                        $warning[$dependent][] =  
                            'This package can not ' 
                            .self::operationToString($this->transaction[$dependent]['operation'])
                            .' because it is used by ' .$this->displayName($reliantPackage);
                        $notice[$reliantPackage][] =
                            'This package needs to be disabled or uninstalled to '
                            .self::operationToString($this->transaction[$dependent]['operation'])
                            .' ' .$this->displayName($dependent);
                    }
                    break;

                // if this package is relied on by other packages but those
                // packages are not being used we are good to go
                case self::STATUS_UNACCESSIBLE:
                case self::STATUS_UNINSTALLED:
                case self::STATUS_DISABLED:
                    break;
            }

            if (array_key_exists($reliantPackage, $this->transaction)) {
                // if this package is part of the transaction and has no
                // errors or warnings then place it in the 'ok' array
                if (empty($error[$reliantPackage]) && empty($warning[$reliantPackage])) {
                    $operation = $this->transaction[$reliantPackage]['operation'];
                    $ok[$reliantPackage][] =
                        ucfirst(self::operationToString($operation))
                        .' ' .$this->displayName($reliantPackage);
                }
            }
        }
    }

    /**
     * Ensure that the packages in the transaction that will be intergrating
     * with the system (ie: downgrade, install, upgrade, enable) will not
     * cause/have problems
     *
     * @param array The shared messages array for the validate functions
     */
    public function validateIntegration(&$results) {
        // set up some pointers to our return
        $error = &$results['error'];
        $warning = &$results['warning'];
        $notice = &$results['notice'];
        $ok = &$results['ok'];

        $dependencyGraph = array();
        foreach ($this->catalog as $packageName => $package) {
            $dependencyGraph[$packageName] = array();
            
            foreach($package['required'] as $requiredPackage => $version) {
                $dependencyGraph[$packageName][] = $requiredPackage;
            }
        }

        // create a list of all the packages that are require to satisfy the installation
        // requirements of this packageList (in the order)
        $requiredPackages = array();
        foreach ($this->transaction as $packageName => $package) {
            switch($package['operation']) {
                case self::OPERATION_DOWNGRADE:
                case self::OPERATION_INSTALL:
                case self::OPERATION_UPGRADE:
                case self::OPERATION_ENABLE:
                case self::OPERATION_VERIFY:
                case self::OPERATION_REPAIR:


                    /**
                     * TODO: This is a temporary nasty hack until I can make
                     * the dependency xor, or, and not work correctly...
                     */
                    if ($packageName == 'asterisk')
                    {
                        if ($this->packageStatus('freeswitch') == self::STATUS_INSTALLED)
                        {
                            $error[$packageName][] = 'The Asterisk Driver can not be installed with the FreeSwitch Driver';
                        }
                    }
                    else if ($packageName == 'freeswitch')
                    {
                        if ($this->packageStatus('asterisk') == self::STATUS_INSTALLED)
                        {
                            $error[$packageName][] = 'The FreeSwitch Driver can not be installed with the Asterisk Driver';
                        }
                    }
                    
                    try {
                        $dependencies = $this->graphReliance($dependencyGraph, $packageName);
                        $dependencies = array_flip($dependencies);
                        foreach ($dependencies as $key => $value) {
                            $dependencies[$key] = array($packageName);
                        }
                        $requiredPackages = array_merge_recursive($dependencies, $requiredPackages);
                    } catch(PackageManager_Catalog_Exception $e) {
                        $error[$packageName][] = $e->getMessage();
                    } catch(PackageManager_Dependency_Exception $e) {
                        $error[$packageName][] = $e->getMessage();
                    }

//      TODO: THIS
//                    // ensure any packages in the not array are not installed
//                    if (isset($package['required']['not'])) {
//
//                        if (!is_array($package['required']['not'])) {
//                            $package['required']['not'] = array($package['required']['not'] => 0.0);
//                        }
//
//                        foreach ($package['required']['not'] as $notPackageName => $version) {
//                            switch($this->packageStatus($notPackageName)) {
//
//                                case self::STATUS_INSTALLED:
//                                    if ($this->versionAvaliable($notPackageName, $packageName, 'not')) {
//                                        $error[$packageName][] =
//                                            'This package is incompatiable with package '
//                                            .$notPackage;
//                                        continue;
//                                    }
//                                    break;
//
//                            }
//                        }
//                    }
//
//                    // ensure at least one package in the or array is installed
//                    if (isset($package['required']['or'])) {
//
//                        if (!is_array($package['required']['or'])) {
//                            $package['required']['or'] = array($package['required']['or'] => 0.0);
//                        }
//
//                        $accumulator = FALSE;
//                        foreach ($package['required']['or'] as $orPackageName => $version) {
//                            switch($this->packageStatus($orPackageName)) {
//
//                                case self::STATUS_INSTALLED:
//                                    $accumulator |= $this->versionAvaliable($orPackageName, $packageName, 'or');
//                                    break;
//
//                            }
//                        }
//
//                        if (!$accumulator) {
//                            $error[$packageName][] =
//                                'This package requires  '
//                                .implode(', ', array_keys($package['required']['or']));
//                        }
//
//                    }
//
//                    // ensure one and only one package in the xor array is not installed
//                    if (isset($package['required']['xor'])) {
//
//                        if (!is_array($package['required']['xor'])) {
//                            $package['required']['xor'] = array($package['required']['xor'] => 0.0);
//                        }
//
//                        foreach ($package['required']['xor'] as $notPackage => $version) {
//                            switch($this->packageStatus($notPackage)) {
//
//                                case self::STATUS_INSTALLED:
//                                    if ($this->versionAvaliable($notPackage, $packageName, 'xor')) {
//                                        $error[$packageName][] =
//                                            'This package is xor with package '
//                                            .$notPackage;
//                                        continue;
//                                    }
//                                    break;
//
//                            }
//                        }
//                    }

                    break;
            }
        }
        
        // enforce the dependencies
        foreach ($requiredPackages as $requiredPackage => $dependents) {

            // if the package has a core requirement check that seperatly
            try {
                $package = &$this->getPackage($requiredPackage);
                if (!empty($package['required']['core'])) {
                    if (!self::compareVersion(Bluebox_Controller::$version, $package['required']['core'])) {
                        $error[$requiredPackage][] =
                            'The Bluebox core does not met the version requirement of ' .$this->displayName($requiredPackage);
                        foreach($dependents as $dependent) {
                            if ($dependent == $requiredPackage) continue;
                            $warning[$dependent][] =
                                'This package requires ' .$this->displayName($requiredPackage) .' but it can not be installed on this system';
                        }
                    }
                }
            } catch (PackageManager_Catalog_Exception $e) {}

            switch($this->packageStatus($requiredPackage)) {

                case self::STATUS_UNACCESSIBLE:
                    // if the required package is unaccessible then anything that
                    // depends on it can not be installed
                    foreach($dependents as $dependent) {
                        $error[$dependent][] = 'The required package ' .$this->displayName($requiredPackage) .' could not be found';
                    }
                    break;

                case self::STATUS_UNINSTALLED:
                    // if a required package is uninstalled
                    // then anything that depends on it can not be installed
                    foreach($dependents as $dependent) {
                        if ($dependent == $requiredPackage) continue;
                        // check if this meets the version requirement
                        if (!$this->versionAvaliable($requiredPackage, $dependent)) {
                            $error[$dependent][] =
                                'Unable to find the required version of package '
                                .$this->displayName($requiredPackage);
                            continue;
                        }
                        $warning[$dependent][] =
                            'This package requires ' .$this->displayName($requiredPackage) .' to also be installed';
                        $notice[$requiredPackage][] =
                            'This package needs to be installed due to dependencies of ' .$this->displayName($dependent);
                    }
                    break;

                case self::STATUS_DISABLED:
                    // if a required package is disabled then anything that depends
                    // on it can still install but will not be able to be enabled...
                    foreach($dependents as $dependent) {
                        if ($dependent == $requiredPackage) continue;
                        // check if this meets the version requirement
                        if (!$this->versionAvaliable($requiredPackage, $dependent)) {
                            $error[$dependent][] =
                                'Unable to find the required version of package '
                                .$this->displayName($requiredPackage);
                            continue;
                        }
                        // ... unless we are trying to become enabled at which point this is
                        // a blocking error
                        $operation = $this->transaction[$dependent]['operation'];
                        if ($operation == self::OPERATION_ENABLE) {
                            $warning[$dependent][] =
                                'This package can not be enabled until ' .$this->displayName($requiredPackage) .' is also enabled';
                        } else {
                            $notice[$dependent][] =
                                'This package can not be enabled until ' .$this->displayName($requiredPackage) .' is also enabled';
                        }
                    }
                    break;
                    
                case self::STATUS_INSTALLED:
                    foreach($dependents as $dependent) {
                        if ($dependent == $requiredPackage) continue;
                        // check if this meets the version requirement
                        if (!$this->versionAvaliable($requiredPackage, $dependent)) {
                            $error[$dependent][] =
                                'Unable to find the required version of package '
                                .$this->displayName($requiredPackage);
                            continue;
                        }
                    }
                    break;
            }

            if (array_key_exists($requiredPackage, $this->transaction)) {
                // if this package is part of the transaction and has no
                // errors or warnings then place it in the 'ok' array
                if (empty($error[$requiredPackage]) && empty($warning[$requiredPackage])) {
                    $operation = $this->transaction[$requiredPackage]['operation'];
                    if ($operation == self::OPERATION_VERIFY) {
                        $ok[$requiredPackage][] =
                            $this->displayName($requiredPackage) .' passed verification';
                    } else {
                        $ok[$requiredPackage][] =
                            ucfirst(self::operationToString($operation))
                            .' ' .$this->displayName($requiredPackage);
                    }
                }
            }
        }
    }

    public function runPackageChecks(&$results){
        // set up some pointers to our return
        $error = &$results['error'];
        $warning = &$results['warning'];
        $notice = &$results['notice'];
        $ok = &$results['ok'];

        foreach ($this->transaction as $packageName => $package) {
            if (empty($package['instance']) || !is_object($package['instance'])) continue;
            switch($package['operation']) {
                case self::OPERATION_DOWNGRADE:
                case self::OPERATION_INSTALL:
                case self::OPERATION_UPGRADE:
                case self::OPERATION_ENABLE:
                case self::OPERATION_VERIFY:
                case self::OPERATION_REPAIR:


                    // TODO: THIS IS A NASTY HACK UNTIL NOT, OR, XOR WORKS
                    if (strcasecmp($packageName, 'asterisk'))
                    {
                        if(self::packageStatus('freeswitch') > 0)
                        {
                            $error[$packageName][] = 'This package can not be installed with FreeSwitch';
                        }
                    }

                    $checkMethods = get_class_methods($package['configureClass']);
                    $checkMethods = array_filter($checkMethods, array(
                        __CLASS__,
                        '_filterCheckMethods'
                    ));

                    if (empty($checkMethods) || !is_array($checkMethods)) {
                        break;
                    }

                    // For each method found run it and build a results array with the result
                    foreach($checkMethods as $checkMethod) {
                        // Call the function and get the returned result
                        try {
                            $return = call_user_func(array($package['instance'], $checkMethod));

                            if ($return === FALSE) {
                                $error[$packageName][] = 'Unspecified error occured in during "' .$checkMethod .'" for ' .$this->displayName($packageName);
                            } else if (is_string($return)) {
                                $error[$packageName][] = $return;
                            }

                            if (isset($return['error'])) {
                                $error[$packageName] = array_merge_recursive((array)$error[$packageName], (array)$return['error']);
                            }

                            if (isset($return['warning'])) {
                                $warning[$packageName] = array_merge_recursive((array)$warning[$packageName], (array)$return['warning']);
                            }

                            if (isset($return['notice'])) {
                                $notice[$packageName] = array_merge_recursive((array)$notice[$packageName], (array)$return['notice']);
                            }
                        } catch(Exception $e) {
                            $error[$packageName][] = $e->getMessage();
                        }
                    }

                    break;
            }
        }
    }

    /**
     * Process a directed graph data structure and
     * determine the path and order necessary to install/uninstall.
     * Throws error if it can not be resolved (missing, circular, ect).
     *
     * @param string The packageName to find the dependency path for
     * @param array Internall array traking resolved dependencies
     * @param array Internall array traking seen dependencies for circular reference detection
     * @return array
     */
    public function graphReliance ($graph, $node, &$resolved = array(), &$unresolved = array()) {
        // Track a list of unresolved nodes for circular reference detection
        $unresolved[] = $node;

        // Find the edges of this graph node
        $edges = $graph[$node];
        foreach (array_values($edges) as $edge) {
            if (in_array($edge, array('not', 'or', 'xor', 'core'))) {
                continue;
            }
            // if this edge is not already on the list then add it
            if (array_search($edge, $resolved) === FALSE) {
                // if we have tried to resolve this before but couldnt then
                // it is a circular reference (they are looping between eachother)
                if (array_search($edge, $unresolved) !== FALSE) {
                    throw new PackageManager_Dependency_Exception(
                            'Circular package dependency between '
                            .$this->displayName($edge) .' and '
                            .$this->displayName($node)
                    );
                }
                $this->graphReliance($graph, $edge, $resolved, $unresolved);
            }
        }

        // If we successfully resolved this node (ie got here) remove it from
        // the unresolved list ...
         unset($unresolved[array_search($node, $unresolved)]);

        // ... and add it to the resolved
        $resolved[] = $node;

        return $resolved;
    }

    /**
     * Retrieve the display name of a package if possible, otherwise
     * try to come up with the best looking version of packageName
     *
     * @param string $packageName
     * @return string
     */
    public function displayName($packageName) {
        try{
            $package = $this->getPackage($packageName);
        } catch(PackageManager_Catalog_Exception $e) {
            return ucfirst(inflector::humanize($packageName));
        }

        if (empty($package['displayName'])) {
            return $packageName;
        }
        return $package['displayName'];
    }

    /**
     * Get a packge from the catalog or throw a PackageManager_Package_Exception
     * if it doesnt exits.  If this is in a transaction and the pacakge has been
     * modified then default to the changed package.  This can be overridden
     * by the second parameter
     *
     * @param string The name of the package to retrieve
     * @param bool Consider the transaction
     * @return reference
     */
    public function &getPackage($packageName, $consultTransaction = TRUE) {
        if ($consultTransaction === TRUE) {
            $transaction = &$this->transaction;
            if (array_key_exists($packageName, $transaction)) {
                return $transaction[$packageName];
            }
        }

        $catalog = &$this->catalog;
        if (!array_key_exists($packageName, $catalog)) {
            throw new PackageManager_Catalog_Exception('Unknown package "' .$packageName .'"');
        }

        return $catalog[$packageName];
    }
    
    /**
     * This will determine the current or planed status of a package
     *
     * @param string PackageName of the package to determine the status of
     * @return
     * -1 - Package not in the catalog
     *  0 - Not installed or schedualed for uninstall
     *  1 - Package installed but disabled (or will become disabled)
     *  2 - Package installed and will remain enabled
     */
    public function packageStatus($packageName) {
        if (array_key_exists($packageName, $this->transaction)) {
            $package = &$this->transaction[$packageName];
            switch($package['operation']) {
                case self::OPERATION_DOWNGRADE:
                case self::OPERATION_INSTALL:
                case self::OPERATION_UPGRADE:
                case self::OPERATION_ENABLE:
                    return self::STATUS_INSTALLED;
                case self::OPERATION_DISABLE:
                    return self::STATUS_DISABLED;
                case self::OPERATION_UNINSTALL:
                    return self::STATUS_UNINSTALLED;
            }
        }

        try {
            $package = &$this->getPackage($packageName);
        } catch (PackageManager_Catalog_Exception $e) {
            return self::STATUS_UNACCESSIBLE;
        }

        return $package['packageStatus'];
    }

    /**
     * Determines if the package name provided mets the version requirements of
     * the other package.  For specal case checks the logic parameter is used
     * to access the correct required sub-array.
     *
     * @param string The package name of the package that is required
     * @param string The package name of the package that requires the first
     * @param string The special case key of the sub-array for logical arrays
     * @return bool
     */
    public function versionAvaliable($requiredPackageName, $dependentPackageName, $logic = NULL) {
        try {
            $requiredPackage = $this->getPackage($requiredPackageName);
            $dependentPackage = $this->getPackage($dependentPackageName);
        } catch (PackageManager_Catalog_Exception $e) {
            return FALSE;
        }

        if (is_null($logic)) {
            $requiredVersion = &$dependentPackage['required'][$requiredPackageName];
        } else {
            $requiredVersion = &$dependentPackage['required'][$logic][$requiredPackageName];
        }

        if (empty($requiredVersion)) {
            return TRUE;
        }

        $avaliableVersion = $requiredPackage['version'];

        return self::compareVersion($avaliableVersion, $requiredVersion);
    }

    /**
     * A simple wraper for version_compare so future version
     * requirements can be easily added (such as using regex)
     *
     * @return bool
     * @param string $requiredVersion PHP-standardized version number string to check against
     * @param string $avaliableVersion PHP-standardized version number strings to check
     * @param string $operator[optional] Test for a particular relationship.
     *          The possible operators are: <, lt, <=, le, >, gt, >=, ge, ==, eq, !=, <>, ne
     * @return bool
     */
    public static function compareVersion($avaliableVersion, $requiredVersion, $operator = '>=')
    {
        $validOperators = array(
            '!=', '<>', 'ne',
            '<=', 'le',
            '<', 'lt',
            '>=', 'ge',
            '>', 'gt',
            '==', 'eq'
        );

        if (count($logic = explode(' and ', $requiredVersion)) == 2) {
            return self::compareVersion($avaliableVersion, $logic[0], $operator)
                    && self::compareVersion($avaliableVersion, $logic[1], $operator);
        }

        if (count($logic = explode(' or ', $requiredVersion)) == 2) {
            return self::compareVersion($avaliableVersion, $logic[0], $operator)
                    || self::compareVersion($avaliableVersion, $logic[1], $operator);
        }

        // This might seem odd but if the operator is in the version strings there has to be a space....
        if (strstr($requiredVersion, ' ')) {
            // Check the strings for a valid operator
            foreach($validOperators as $validOperator) {
                $validOperator .= ' ';
                if (stristr($requiredVersion, $validOperator)) {
                    $requiredVersion = str_replace($validOperator, '', $requiredVersion);
                    $operator = str_replace(' ', '', $validOperator);
                    break;
                }
            }
        }
        // make the comparision
        if (version_compare($avaliableVersion, $requiredVersion, $operator)) return true;
        else return false;
    }

    /**
     * This function will attemp to integrate a number type if a model extends
     * Number
     *
     * @return bool
     * @param array $models
     */
    public static function integrateNumberType($models, $packageId, $name = '')
    {
        foreach($models as $model) {
            
            if (!class_exists($model) || !is_subclass_of($model, 'Number')) {

                continue;

            }

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

                $numberType->package_id = $packageId;

                $numberType->save();

                $numberType->free(TRUE);

            } catch(Exception $e) {

                throw new PackageManager_Exception($e->getMessage() .print_r($models, true));

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
    public static function removeNumberType($models, $name = '')
    {
        foreach($models as $model) {

            if (!class_exists($model) || !is_subclass_of($model, 'Number')) {

                continue;

            }

            Kohana::log('debug', 'Removing ' . $model . ' from NumberType after disable');

            try {

                $q = Doctrine::getTable('NumberType')->createQuery('n')->where('n.class = ?', $model);

                $numberType = $q->fetchOne();

                // If we have a row then remov it
                if ($numberType) {

                    $numberType->delete();

                    $numberType->free(TRUE);

                }

            } catch(Exception $e) {

                throw new PackageManager_Exception($e->getMessage());

                return FALSE;

            }

        }

        return TRUE;
        
    }

    /**
     * Convert a package status into a human readable string
     * 
     * @param const A status constant
     * @return string
     */
    public static function statusToString ($status) {
        switch($status) {
            case self::STATUS_UNACCESSIBLE:
                return 'unaccessible';
            case self::STATUS_UNINSTALLED:
                return 'uninstalled';
            case self::STATUS_DISABLED:
                return 'disabled';
            case self::STATUS_INSTALLED:
                return 'installed';
            default:
                return 'unknown';
        }
    }

    /**
     * Convert a package operation into a human readable string
     *
     * @param const A operation constant
     * @return string
     */
    public static function operationToString ($operation) {
        switch($operation) {
            case self::OPERATION_DOWNGRADE:
                return 'downgrade';
            case self::OPERATION_INSTALL:
                return 'install';
            case self::OPERATION_UPGRADE:
                return 'upgrade';
            case self::OPERATION_ENABLE:
                return 'enable';
            case self::OPERATION_DISABLE:
                return 'disable';
            case self::OPERATION_REPAIR:
                return 'repair';
            case self::OPERATION_UNINSTALL:
                return 'uninstall';
            case self::OPERATION_VERIFY:
                return 'verify';
            default:
                return 'unknown';
        }
    }

    /**
     * This is the callback filters that list to only those methods
     * that start with '_check'.
     *
     * @return bool True if a method begins with '_check' otherwise false.
     * @param string $methodName The method name to test
     */
    private static function _filterCheckMethods($methodName) {
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
}
class PackageManager_Exception extends Exception {
    //put your code here
}
class PackageManager_Catalog_Exception extends PackageManager_Exception {
    //put your code here
}
class PackageManager_Dependency_Exception extends PackageManager_Exception {
    //put your code here
}