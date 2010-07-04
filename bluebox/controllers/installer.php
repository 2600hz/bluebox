<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * installer.php - Auto-installer. Installs Bluebox and initializes modules.
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Installer
 */
class Installer_Controller extends Bluebox_Controller
{
    /**
     * @var bool $autoloadUser do not allow our parent class to load the user as they dont exist yet!
     */
    public $autoloadUser = FALSE;
    /**
     * @var array $defaultLanguages list of supported langauges were keys are the short name and values the display name
     */
    public $defaultLanguages = array(
        'EN_US' => 'English (US)',
        'ES' => 'Español'
    );
    /**
     * @var array $defaultCurrencies list of supported currencies
     */
    public $defaultCurrencies = array(
        'USD' => 'US Dollars ($)',
        'CAD' => 'Canadian Dollars ($)',
        'EUR' => 'Euro (€)',
        'GBP' => 'British Pounds (£)',
        'JPY' => 'Japanese Yen (¥)'
    );
    /**
     * @var string $template name of the installer base template
     */
    public $template = 'skins/installer/';
    /**
     *
     * @var int $log_threshold the log_threshold starts at 0 and this is what to set to post-install
     */
    public $log_threshold = 2;
    /**
     * @var array $steps List of functions to process for install (order is important)
     */
    //protected $steps = array('welcome', 'testEnvironment', 'installMode', 'configure', 'choosePackages', 'doInstall', 'finalize');
    protected $steps = array(
        'welcome',
        'testEnvironment',
        'configure',
        'createAdmin',
        'telephony',
        'choosePackages',
        'doInstall',
        'finalize'
    );
    /**
     * @var array $pluginEvents List of events to run, set per step
     */
    protected $pluginEvents = array();
    /**
     * This constructor sets up session, pulls the current install wizard step and initializes the template
     *
     * @return void
     */
    public function __construct()
    {
        if (!Kohana::config('config.installer_enabled')) {
            throw new Exception('The installer has been administratively disabled. (You can re-enable it in bluebox/config/config.php)');
        }
        $siteDomain = str_replace('installer', '', Kohana::config('core.site_domain'));
        Kohana::config_set('core.site_domain', '/' . trim($siteDomain, '/') . '/');
        skins::setSkin($this->template);
        parent::__construct();
        /**
         * TODO: Remove me when i18n is more stable
         */
        $this->session->set('lang', 'en');
        // Attempt to retrieve the current Step
        $this->currentStepKey = $this->session->get('installer.currentStepKey', 0);
        $this->currentStep = $this->steps[$this->currentStepKey];
        // If this step is before the environment test the disable logging because we dont know if
        // kohana has write permissions on logs/
        if ($this->currentStepKey > 1 && Kohana::config('core.log_threshold') == 0) {
            Kohana::config_set('core.log_threshold', $this->log_threshold);
        }
        // This is the default list of steps to run, modified to work with the wizard
        $this->pluginEvents = array(
            'core' => Router::$controller,
            'coreAction' => Router::$controller . '.' . $this->currentStep
        );
    }
    /**
     * This is the main wizard engine.  It will process the steps in the protected $steps looking for
     * methods that match the step name.  When information is submitted back and a method called process{stepName}
     * exists then the post var will be handed to it at which point that method chooses to continue
     * to the next step (after processing the post) by returning true; otherwise if the process method of a
     * step does not exist then it automaticly advances to the next or previous step.
     *
     * @return void
     */
    public function index()
    {
        /**
         * TODO: this is temporarliy here until the installMode step is but back
         */
        $this->session->set('installer.installMode', 'install');
        /**
         * These vars are used to execute event hooks
         */
        $this->views = $this->template->views = array();
        // Get any responses
        $returns = $this->input->post();
        // If there are return values try to process them!
        if (!empty($returns)) {
            // This test makes sure that the form being submitted has a matching token,
            // since the token changes every time the form is rendered if the tokens dont
            // match then it must be a refresh....
            $testToken = $this->session->get('installer.formToken', false);
            if (empty($returns['form_token']) || $returns['form_token'] != $testToken) {
                if (isset($returns['next'])) unset($returns['next']);
                if (isset($returns['prev'])) unset($returns['prev']);
            }
            // Save all the submits from this form
            $ignoreReturns = array(
                'next',
                'prev',
                'license',
                'formToken'
            );
            foreach($returns as $name => $return) {
                if (!in_array($name, $ignoreReturns)) $this->session->set('installer.' . $name, $return);
            }
            // See if the form was submitted with the next button
            if (!empty($returns['next'])) {
                // If the user wants to continue check for the existance of a method called process{stepName}
                if (method_exists($this, 'process' . ucfirst($this->currentStep))) {
                    // It the process exists go to the next step on if this step returns true
                    $proceed = call_user_func(array(
                        $this,
                        'process' . ucfirst($this->currentStep)
                    ));
                } else {
                    $proceed = true;
                }
                // Run all registered save hooks for this installer.step
                $proceed = $proceed & plugins::save($this, $this->pluginEvents);
                $this->_nextWizard($proceed);
                // If the user wants to go back on step who are we to stop them?
                
            } else if (!empty($returns['prev'])) {
                $this->_prevWizard();
            }
        }
        // Lets generate a unique token that the form must respond with so on refresh
        // it doesnt progress unexpectedly
        $formToken = strtoupper(md5(uniqid(rand() , true)));
        $this->session->set('installer.formToken', $formToken);
        $this->template->formToken = $formToken;
        // Default to allow them to go to the back a step if the are not on the first.
        $this->template->allowPrev = $this->currentStepKey > 0 ? true : false;
        // By default they can always continue (but a step may disable this ability!)
        $this->template->allowNext = true;
        // Attempt to render this step
        try {
            $subview = call_user_func(array(
                $this,
                $this->currentStep
            ));
            // Set the step view in the main template
            $this->template->content = $subview;
        }
        catch(Exception $e) {
            $this->template->title = __('INSTALLATION ERROR');
            $this->template->allowPrev = false;
            $this->template->allowNext = false;
            $subview = '<div class="error">' . i18n('ERROR: Can not execute step %s, wizard terminated!', $this->currentStep)->sprintf()->s() . '</div>';
            $this->session->destroy();
            // Set the step view in the main template
            $this->template->content = $subview;
        }
        // Run all registered view hooks for this installer.step
        plugins::views($this, $this->pluginEvents);
        $this->template->views = $this->template->content->views;
    }
    /**
     * This attempts to uncomment the index_page in config.php if mod_rewrite is not
     * on or not allowed.
     *
     * @return void
     */
    public function fixModRewrite()
    {
        Kohana::config_set('core.site_domain', '/' . url::guess_site_domain() . '/');
        $indexPage = Kohana::config('core.index_page');
        if (!empty($indexPage)) url::redirect('/installer');
        Kohana::config_set('core.index_page', 'index.php');
        // Get the current config.php file
        if ($files = Kohana::find_file('config', 'config')) $file = @file(end($files));
        // Make sure we were sucessfull
        if (empty($file)) {
            // Use the preLog because we dont know if we have write permissions on logs/ yet!
            $preLog = $this->session->get('installer.pre_log');
            $preLog['error'][] = 'Could not locate or read config.php during mod_rewrite fix!';
            $this->session->set('installer.pre_log', $preLog);
            url::redirect('/index.php/installer?config_file=config');
        }
        foreach($file as $num => $line) {
            preg_match('/.*[\'"`]([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\'"`].*$/imx', $line, $result);
            if (!empty($result[1]) && strstr($result[1], 'index_page')) $file[$num] = ltrim($line, '/#;');
        }
        $file = implode('', $file);
        // If we got the file then we must have made changes, attempt to save it back
        // but if there is an error doing so have the user do it
        if (@file_put_contents(end($files) , $file) === false) {
            // Use the preLog because we dont know if we have write permissions on logs/ yet!
            $_GET['config_file'] = 'config';
            $this->viewCache($file);
            $this->template->allowNext = true;
            return false;
        }
        url::redirect('/installer');
    }
    /**
     * This function is used by the configure step to show the new contents of a file if
     * we are unable to write to that file
     *
     * @return void
     */
    public function viewCache($configContents = '')
    {
        $cache_file = empty($_GET['config_file']) ? 'no-such-file' : $_GET['config_file'];
        $configFile = Kohana::find_file('config', $cache_file);
        $configFile = is_array($configFile) ? end($configFile) : $configFile;
        $this->template->title = i18n('Manual %s Config Update', ucfirst($cache_file))->sprintf()->s();
        $this->template->formToken = ' ';
        $this->template->views = array();
        if (empty($configFile)) $configFile = i18n('File %s does not exist or can not be read!', $cache_file . '.php')->sprintf()->s();
        if (empty($configContents)) {
            $cache = Cache::instance();
            $configContents = $cache->find($cache_file . '_file');
            // This orders the cache by creation so we show the newest
            ksort($configContents, SORT_NUMERIC);
            $configContents = end($configContents);
        }
        if (!empty($configContents)) {
            message::set('Please manually replace the contents of ' . $cache_file . '.php!<div>You can also change the permissions so the installer can write to it.</div>', 'info');
            $this->template->content = '<div>' . $configFile . '</div>';
            $this->template->content.= '<textarea style="width: 100%; height: 300px;">' . $configContents . '</textarea>';
        } else {
            $this->template->content = '';
            message::set('Oops, I can\'t find the new content for ' . $cache_file . '.php! Sorry guess you are on your own...');
        }
        $this->template->allowPrev = false;
        $this->template->allowNext = false;
    }
    /**
     * This function erases all installer session vars
     * and redirects to the beging
     *
     * @return void
     */
    public function reset()
    {
        self::_resetWizard();
        url::redirect('/installer');
    }
    /**
     * This function allows developers to skip steps (such as telephony);
     * however, you better know what you are doing!
     *
     * @return void
     */
    public function skip_step()
    {
        $this->_nextWizard();
        url::redirect('/installer');
    }
    public function updateConfig($configMap, $config)
    {
        // We are going to use the cache engine to store our files while we work.
        $cache = Cache::instance();
        // Get the current config files
        if ($files = Kohana::find_file('config', $config)) $file = @file(end($files));
        // Make sure we were sucessfull
        if (empty($file)) {
            message::set('Could not locate or read ' . $config . '.php!');
            return false;
        }
        // Compare what we where given to what is in the file and replace what differs
        if (self::_replaceConfig($configMap, $file)) {
            $file = implode('', $file);
            // If we got the file then we must have made changes, attempt to save it back
            // but if there is an error doing so have the user do it
            if (@file_put_contents(end($files) , $file) === false) {
                $cache->set(time() , $file, $config . '_file');
                message::set('Unable to write to ' . $config . '.php, please manualy replace it with ' . html::anchor('installer/viewCache?config_file=' . $config, 'this!', array(
                    'target' => '_blank'
                )));
                return false;
            }
        }
        return true;
    }
    /************************************************************************
    *						 INSTALL WIZARD STEPS							*
    *************************************************************************/
    /**
     * This step shows a welcome screen with various info such as minimum requirements
     *
     * @return subview
     */
    private function welcome()
    {
        $subview = new View('installer/welcome');
        $this->template->title = __('Welcome to the Bluebox Setup Wizard - version ' . Bluebox_Controller::$version);
        // Load any previous settings
        $subview->acceptLicense = $this->session->get('installer.acceptLicense');
        // Based on the selected language append a file name to the license directory
        $license_file = APPPATH . 'views/installer/EN_US_LICENSE.TXT';
        // If that license exists set it to be shown in the subview
        if (file_exists($license_file)) {
            $subview->license = file_get_contents($license_file);
        } else {
            // If the license doesnt exist direct the user to view it at some website
            $subview->license = __('The license file could not be located.  Please read the license at ') . 'http://www.mozilla.org/MPL/MPL-1.1.html';
            message::set('You are still legally accepting the license so please ensure you read it!', 'alert');
        }
        return $subview;
    }
    /**
     * Process the license step and only allows the next step if the user accepts the terms
     *
     * @return bool true if terms accepted otherwise false
     * @param object $return[optional] The form return values to check accpteLicense
     */
    private function processWelcome()
    {
        $acceptLicense = $this->session->get('installer.acceptLicense', false);
        if (!empty($acceptLicense)) {
            return true;
        }
        message::set('Please accept the license to continue!');
        return false;
    }
    /**
     * This step looks for methods in this class that start with '_check' and executes each of them
     * in the order it finds them.  It expects to get a result array back with information about
     * if the environment is capable of supporting the bluebox dependency each method is designed to check.
     *
     * @return subview
     */
    private function testEnvironment()
    {
        $subview = new View('installer/testEnvironment');
        $this->template->title = __('Test Environment');
        // Every time we recheck the environment we need to rest any prevous failure marker
        $this->session->delete('installer.environmentTestFailed');
        $subview->results = array();
        $this->session->set('installer.pre_stats', array());
        // Create a list of methods in this class that start with '_check' and can be executed
        $checkMethods = get_class_methods($this);
        $checkMethods = array_filter($checkMethods, array(
            $this,
            '_filterCheckMethods'
        ));
        // For each method found run it and build a results array with the result
        $hasErrors = FALSE;
        $hasWarnings = FALSE;
        foreach($checkMethods as $checkMethod) {
            // Call the function and get the returned result
            $result = call_user_func(array(
                $this,
                $checkMethod
            ));
            // Check the result to track if we fail either the required or optional dependencies
            if (!$result['result']) {
                if ($result['required']) {
                    $hasErrors = TRUE;
                } else {
                    $hasWarnings = TRUE;
                }
            }
            // Build the final results array so the user can see the overal status
            $subview->results[] = $result;
        }
        if (!empty($hasErrors)) {
            message::set('Installation can not continue until dependiencies have been met! Refresh to test again.');
            // Set a session var so we can tell if we should be allowed to continue without re-running all the methods
            $this->session->set('installer.environmentTestFailed', true);
        }
        if (!empty($hasWarnings)) {
            message::set('Installation can continue but functionality may be reduced! Refresh to test again.', 'alert');
        }
        return $subview;
    }
    /**
     * This process the return to the next step from the environment test.  If a required
     * dependency has failed then it stops the wizard by returning false.  Otherwise it
     * returns true.
     *
     * @return bool False if a required dependency is missing otherwise true
     * @param object $return[optional] The form return values, inconsequential to this method
     */
    private function processTestEnvironment()
    {
        $environmentGood = $this->session->get('installer.environmentTestFailed', false) ? false : true;
        if ($environmentGood) {
            // Untill this point we couldnt write to the log because we may not have had permissions in logs/
            // so write out everything that was stored now that we know we can.
            Kohana::config_set('core.log_threshold', $this->log_threshold);
            return true;
        }
        return false;
    }
    /**
     * This step allows the user to choose either install or upgrade
     *
     * @return subview
     */
    private function installMode()
    {
        $subview = new View('installer/installMode');
        $this->template->title = __('Installation Mode');
        // Send any previous response back to to form, otherwise set the default
        $subview->installMode = $this->session->get('installer.installMode', 'install');
        return $subview;
    }
    /**
     * This process does not allow the user to continue if they choose upgrade
     *
     * TODO: No such option for upgrade yet...
     *
     * @return bool false if user requested upgrade otherwise true
     * @param object $return[optional] The form return values
     */
    private function processInstallMode($returns = '')
    {
        if (!empty($returns['installMode']) && $returns['installMode'] == 'upgrade') {
            message::set('That function is unsupported at this time!');
            // some mechanism to change the steps here....
            return false;
        }
        return true;
    }
    /**
     * This step requires the user to configure the default settings
     *
     * @return subview
     */
    private function configure()
    {
        $subview = new View('installer/configure');
        $this->template->title = __('Initial Configuration');

        // Find the intersection of the available drivers and those that doctrine supports
        $availableDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getAvailableDrivers();
        $supportedDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getSupportedDrivers();
        $drivers = array_uintersect($availableDrivers, $supportedDrivers, 'strcasecmp');
        // Set the keys equal to the value so the select will return meaningfull values
        foreach($drivers as $driver) $driversPDO[$driver] = $driver;
        $defaultType = Kohana::config('database.default.connection.type', reset($driversPDO));
        if (!in_array($defaultType, $driversPDO)) $defaultType = reset($driversPDO);
        $subview->dbType = $this->session->get('installer.dbType', $defaultType);
        $subview->dbTypes = $driversPDO;

        if (stristr($subview->dbType, 'sqlite')) {
            $subview->dbPathName = $this->session->get('installer.dbPathName', Kohana::config('database.default.connection.host'));
            $subview->dbHostName = $this->session->get('installer.dbHostName', '127.0.0.1');
        } else {
            $subview->dbHostName = $this->session->get('installer.dbHostName', Kohana::config('database.default.connection.host'));
            $subview->dbPathName = $this->session->get('installer.dbPathName');
        }
        $port = Kohana::config('database.default.connection.port');
        if (empty($port)) $port = '';
        $subview->dbPortSelection = $this->session->get('installer.dbPortSelection', $port);

        $subview->dbUserName = $this->session->get('installer.dbUserName', Kohana::config('database.default.connection.user'));
        $subview->dbUserPwd = $this->session->get('installer.dbUserPwd', Kohana::config('database.default.connection.pass'));
        $subview->dbName = $this->session->get('installer.dbName', Kohana::config('database.default.connection.database'));
        $subview->dbPersistent = $this->session->get('installer.dbPersistent', Kohana::config('database.default.persistent'));
        // Passback or setup the site domain
        $subview->autoURI = $this->session->get('installer.siteDomain', Kohana::config('core.site_domain'));
        // Passback or setup the upload dir
        $subview->uploadDir = $this->session->get('installer.uploadDir', Kohana::config('upload.directory'));
        // Get the langauge if not specified default to english (US)
        $subview->defaultLanguage = $this->session->get('installer.language', 'EN_US');
        $subview->defaultLanguages = $this->defaultLanguages;
        // Get the default timezone
        $subview->defaultTimeZone = $this->session->get('installer.defaultTimeZone');
        // Get the currency if not specified default to US Dollars (USD)
        $subview->defaultCurrency = $this->session->get('installer.defaultCurrency', 'USD');
        $subview->defaultCurrencies = $this->defaultCurrencies;
        // Get the installSample option
        $subview->samples = $this->session->get('installer.samples', TRUE);
        // Get the statistics option
        $allowCollection = $this->session->get('installer.collectStatistics', Kohana::config('core.anonymous_statistics'));
        $subview->collectStatistics = $allowCollection;
        return $subview;
    }
    /**
     * Validate form and test db connection
     *
     * @return bool true if db can connect and form is valid
     */
    private function processConfigure()
    {
        // This both ensures that there is a / on both the front and back of siteDomain
        // but also if the is no actual domain the string remains empty (ie NOT //)
        $siteDomain = $this->session->get('installer.siteDomain', Kohana::config('core.site_domain'));
        $siteDomain = trim($siteDomain, '/');
        $siteDomain = empty($siteDomain) ? '/' : '/' . $siteDomain . '/';
        // This changes the log_threshold if it is not already changed by the user
        if (($log_threshold = Kohana::config('core.log_threshold')) == 0) $log_threshold = $this->log_threshold;
        // This array maps the database returns to the database config file
        $databaseOptions = array(
            'type' => $this->session->get('installer.dbType') ,
            'host' => $this->session->get('installer.dbHostName') ,
            'port' => $this->session->get('installer.dbPortSelection') ,
            'user' => $this->session->get('installer.dbUserName') ,
            'pass' => $this->session->get('installer.dbUserPwd') ,
            'database' => $this->session->get('installer.dbName') ,
            'persistent' => (bool)$this->session->get('installer.dbPersistent') ,
        );
        // This array maps the configuration returns to the config file
        $configOptions = array(
            'site_domain' => $siteDomain,
            'anonymous_statistics' => (bool)$this->session->get('installer.collectStatistics'),
            'anonymous_id' => Anonymous_Statistics::getID() ,
            'log_threshold' => $log_threshold
        );
        // This array maps the configuration to the locale file
        $localeOptions = array(
            'timezone' => $this->session->get('installer.defaultTimeZone')
        );
        // This array maps the configuration returns to the upload file
        $uploadOptions = array(
            'directory' => $this->session->get('installer.uploadDir')
        );
        // Check if this will be using sqlite
        if (stristr($databaseOptions['type'], 'sqlite')) {
            $databaseOptions['host'] = $this->session->get('installer.dbPathName');
            if (!file_exists($databaseOptions['host'])) {
                message::set('SQLite path does not exist or can not be read!');
                return FALSE;
            }
        }
        // Ensure we have everything we need
        if (empty($databaseOptions['type']) || empty($databaseOptions['host']) || empty($databaseOptions['database'])) {
            message::set('Minimum database configuration not provided!');
            return FALSE;
        }
        // If the user wants to use a custom port then set that up
        if (empty($databaseOptions['port'])) {
            $databaseOptions['port'] = 'FALSE';
        }
        // anonymous statistics need to be a quoted bool for the config parser
        if (empty($configOptions['anonymous_statistics'])) {
            $configOptions['anonymous_statistics'] = 'FALSE';
        } else {
            $configOptions['anonymous_statistics'] = 'TRUE';
        }
        // database persistents need to be a quoted bool for the config parser
        if (empty($databaseOptions['persistent'])) {
            $databaseOptions['persistent'] = 'FALSE';
        } else {
            $databaseOptions['persistent'] = 'TRUE';
        }
        // Get the doctrine overlord
        $manager = Doctrine_Manager::getInstance();
        // Close all current DB connections
        foreach($manager as $conn) {
            $manager->closeConnection($conn);
        }
        foreach($databaseOptions as $dbOption => $value) {
            Kohana::config_set('database.default.connection.' . $dbOption, $value);
        }
        // Re-initialize Doctrine
        call_user_func(array(
            'DoctrineHook',
            'bootstrapDoctrine'
        ) , FALSE);
        try {
            // See if we can connect to the db
            $manager->getCurrentConnection()->connect();
        }
        catch(Doctrine_Connection_Exception $e) {
            // If we can not connect to the existing db and we are in upgrade mode
            // then error out
            $installMode = $this->session->get('installer.installMode', 'install');
            if ($installMode != 'install') {
                // We cant resolve this issue without the user
                message::set('Unable to establish a connection to '
                    .$databaseOptions['database']
                    .'! <div class="error_details">' . $e->getMessage() . '</div>'
                );
                return FALSE;
            }
            try {
                // See if we can fix this problem by creating the DB, but only if we are in install mode!
                // If we are in upgrade then the user provided the wrong DB info...
                $response = $manager->createDatabases();
                /**
                 * TODO: Doctrine 1.2 does not seem to throw bad credentials for postgres
                 *       ....REPORT THIS TO DOCTRINE!
                 */
                if (!is_string($response['BlueboxDB'])) {
                    $conn = $manager->getCurrentConnection();
                    $conn->rethrowException($response['BlueboxDB'], $conn);
                }
            }
            catch(Exception $e) {
                // We cant resolve this issue without the user
                message::set('Unable to establish a connection to ' .$databaseOptions['type'] .'! <div class="error_details">' . $e->getMessage() . '</div>');
                return FALSE;
            }
            $this->session->set('installer.ensureInstall', $databaseOptions['type']);
        }
        $ensureInstall = $this->session->get('installer.ensureInstall', 'none');
        // If this db exists, we are preforming a fresh install, and the user hasn't been warned.
        // Force them to go around again accepting this fact!
        if ($ensureInstall != $databaseOptions['type']) {
            message::set('The existing database will be permanently erased if you continue!');
            message::set('Click next again to proceed...', 'alert');
            // This session var lets the user continue the second time around (after the warning)
            $this->session->set('installer.ensureInstall', $databaseOptions['type']);
            return false;
        }
        // Write $configOptions to config.php
        if (!self::updateConfig($configOptions, 'config')) return false;
        // Write $localeOptions to locale.php
        if (!self::updateConfig($localeOptions, 'locale')) return false;
        // Write $databaseOptions to database.php
        if (!self::updateConfig($databaseOptions, 'database')) return false;
        // Write $uploadOptions to upload.php
        if (!self::updateConfig($uploadOptions, 'upload')) return false;
        // Go ahead and enable or disable anonymous stats based on the user, because we are about to try to use it
        if ($configOptions['anonymous_statistics'] == 'FALSE') {
            Anonymous_Statistics::clear();
            Kohana::config_set('core.anonymous_statistics', false);
        } else {
            Kohana::config_set('core.anonymous_statistics', true);
            // If the user has opted into anonymous_statistics then write out what we have stored in session previously
            $preStats = $this->session->get('installer.pre_stats');
            if (!empty($preStats) && Kohana::config('core.anonymous_statistics')) {
                foreach($preStats as $id => $preStat) {
                    Anonymous_Statistics::addMsg($preStat[0], $preStat[1], $preStat[2], $id);
                }
                $this->session->delete('installer.pre_stats');
            }
            Anonymous_Statistics::addMsg($databaseOptions['type'], 'db_type', 'installer', 'db_type');
        }
        return true;
    }
    private function createAdmin()
    {
        $subview = new View('installer/createAdmin');
        $this->template->title = __('Create Main Administrator');
        $subview->adminEmailAddress = $this->session->get('installer.adminEmailAddress');
        $subview->adminPassword = $this->session->get('installer.adminPassword');
        $subview->adminConfirmPassword = $this->session->get('installer.adminConfirmPassword');
        return $subview;
    }
    private function processCreateAdmin()
    {
        $valid = true;
        if (!valid::email($this->session->get('installer.adminEmailAddress'))) {
            message::set('You must enter a valid email address to continue!');
            $valid = false;
        } elseif (strlen($this->session->get('installer.adminPassword')) < 1) {
            message::set('You need to set a password!');
            $valid = false;
        } elseif ($this->session->get('installer.adminPassword') != $this->session->get('installer.adminConfirmPassword')) {
            message::set('Passwords do not match!');
            $valid = false;
        }
        return $valid;
    }
    /**
     * This step collects the data for the telephony configuration
     *
     * @return object
     */
    private function telephony()
    {
        $subview = new View('installer/telephony');
        $this->template->title = __('Telephony Engine');
        $packages = Bluebox_Installer::listPackages(Bluebox_Installer::TYPE_DRIVER);
        $drivers = array(
            'none' => 'None'
        );
        foreach($packages as $package) {
            $this->session->set('installer.install_' . $package['packageName'], false);
            $drivers[$package['packageName']] = $package['displayName'];
        }
        // Get the driver configured in telephony.php
        $defaultDriver = strtolower(Kohana::config('telephony.driver'));
        // If the driver in telephony doesnt exist on this system then just default to the first driver
        if (!isset($drivers[$defaultDriver])) {
            $defaultDriver = key($drivers);
        }
        $driver = $this->session->get('installer.tel_driver', $defaultDriver);
        $subview->driver = $driver;
        $subview->drivers = $drivers;
        // Add an event for this telephony driver exclusively
        $this->pluginEvents['driver'] = Router::$controller . '.' . $this->currentStep . '.' . $driver;
        return $subview;
    }
    /**
     * This process ensures that the config files are accessable, can be written to
     * and saves the ESL data... currently ESL connections are not tested....
     *
     * @return bool
     */
    private function processTelephony()
    {
        $driver = $this->session->get('installer.tel_driver', 'none');
        if ($driver != 'none') {
            $this->session->set('installer.install_' . $driver, true);
            // Add an event for this telephony driver exclusively
            $this->pluginEvents['driver'] = Router::$controller . '.' . $this->currentStep . '.' . $driver;
        } else {
            // Set the driver name to none in telephony.php if there is no driver
            if (!Installer_Controller::updateConfig(array(
                'driver' => 'none'
            ) , 'telephony')) return false;
        }
        Anonymous_Statistics::addMsg($driver, 'tel_driver', 'installer', 'tel_driver');
        return true;
    }
    /**
     * This step allows the user to select what set of modules to install
     *
     * @return subview
     */
    private function choosePackages()
    {
        $subview = new View('installer/choosePackages');
        $this->template->title = __('Package Selection');
        // Get a list of all the modules on the system
        if ($this->session->get('installer.installMode', 'install') == 'install') $ignoreDB = true;
        else $ignoreDB = false;
        $telDrivers = Bluebox_Installer::listPackages(Bluebox_Installer::TYPE_DRIVER);
        $currentDriver = $this->session->get('installer.tel_driver', 'none');
        if (array_key_exists($currentDriver, $telDrivers)) unset($telDrivers[$currentDriver]);
        $telDrivers = array_keys($telDrivers);
        $packages = Bluebox_Installer::listPackages(array() , $ignoreDB);
        $install = array();
        foreach($packages as $name => $package) {
            foreach ($telDrivers as $telDriver) {
                if (array_key_exists($telDriver, $package['required'])) {
                    unset($packages[$name]);
                    continue 2;
                }
                if ($name == $telDriver) {
                    unset($packages[$name]);
                    continue 2;
                }
            }
            // For each module get the users preference for installation or default to true
            $value = $this->session->get('installer.install_' . $package['packageName'], $package['default']);
            // If the user wants to install this module then build a list so we can check its dependencies
            // Also set the form checkbox appropriately
            $checkboxName = 'install_' .$package['packageName'];
            if (empty($value)) {
                $subview->$checkboxName = false;
            } else {
                $install[] = $package['packageName'];
                $subview->$checkboxName = true;
            }
        }
        // Test the dependencies of all the modules the user wants to install
        $packages = Bluebox_Installer::checkDependencies($packages, $install);
        Bluebox_Installer::warningSort($packages);
        Bluebox_Installer::errorSort($packages);
        // Check if there where any errors or warnings from the dependency checker
        if (!empty(Bluebox_Installer::$errors)) {
            message::set('Installation can not continue until dependiencies have been met! See errors below.');
        }else if (!empty(Bluebox_Installer::$warnings)) {
            message::set('You may continue installation however some packages have potential issues.', 'info');
            $this->session->set('installer.warnings', Bluebox_Installer::$warnings);
        }
        // Pass all the warnings and errors onto the view
        $subview->currentDriver = $currentDriver;
        $subview->packageList = $this->createPackageList($packages, Bluebox_Installer::$errors, Bluebox_Installer::$warnings);
        return $subview;
    }
    private function processChoosePackages()
    {
        // Get a list of all the modules on the system
        if ($this->session->get('installer.installMode', 'install') == 'install') $ignoreDB = true;
        else $ignoreDB = false;
        $telDrivers = Bluebox_Installer::listPackages(Bluebox_Installer::TYPE_DRIVER);
        $currentDriver = $this->session->get('installer.tel_driver', 'none');
        if (array_key_exists($currentDriver, $telDrivers)) unset($telDrivers[$currentDriver]);
        $telDrivers = array_keys($telDrivers);
        $packages = Bluebox_Installer::listPackages(array() , $ignoreDB);
        $install = array();
        foreach($packages as $name => $package) {
            foreach ($telDrivers as $telDriver) {
                if (array_key_exists($telDriver, $package['required'])) {
                    unset($packages[$name]);
                    continue 2;
                }
                if ($name == $telDriver) {
                    unset($packages[$name]);
                    continue 2;
                }
            }
            if ($name == $currentDriver) {
                $this->session->set('installer.install_' . $package['packageName'], true);
                $install[] = $package['packageName'];
                continue;
            }
            // For each module get the users preference for installation or default to true
            $value = $this->session->get('installer.install_' . $package['packageName'], 'false');
            // If the user wants to install this module then build a list so we can re-check its dependencies
            if (!empty($value)) $install[] = $package['packageName'];
        }
        // Test the dependencies of all the modules the user wants to install
        Bluebox_Installer::checkDependencies($packages, $install);
        // Ensure there are no new warnings
        $old_warnings = $this->session->get('installer.warnings', array());
        //if (!arr::array_compare_recursive(Bluebox_Installer::$warnings, $old_warnings)) return FALSE;
        // If all the dependencies are met let the user continue
        return empty(Bluebox_Installer::$errors);
    }
    /**
     * This step installs stuff
     *
     * @return subview
     */
    private function doInstall()
    {
        $subview = new View('installer/doInstall');
        if ($this->session->get('installer.installMode', 'install') == 'install') {
            $this->template->title = __('Installation');
            $subview->process = __('Install');
        } else {
            $this->template->title = ucfirst(__('upgrade'));
            $subview->process = __('upgrade');
        }
        return $subview;
    }
    private function processDoInstall()
    {
        if ($this->session->get('installer.installMode') == 'install') {
            $result = self::_freshInstall();
        } else {
            $result = self::_existingUpgrade();
        }
        if ($result != false && empty(Bluebox_Installer::$errors)) {
            Kohana::log('info', 'Install wizard completed successfully');
            return true;
        } else {
            if (empty($this->template->error)) {
                message::set('Install failed with errors!'
                    .'<div>'
                    .implode('<br>', Bluebox_Installer::$errors)
                    .'</div>'
                );                
                
            }
            return false;
        }
    }
    /**
     * This step finalizes the installation (whatever that means)
     *
     * @return subview
     */
    private function finalize()
    {
        $subview = new View('installer/finalize');
        $this->template->title = __('Complete!');
        $this->template->allowPrev = false;
        $this->template->allowNext = false;
        self::_resetWizard();
        $this->session->delete('bluebox_message');

        // Disable the installer after a successful installtion
        self::updateConfig(array('installer_enabled' => 'FALSE'), 'config');

        Kohana::log('info', 'Installer wizard terminated');
        return $subview;
    }
    /************************************************************************
    *				 INSTALL WIZARD SUPPORT METHODS							*
    *************************************************************************/
    /**
     * This method advances to the next step in the wizard and saves it to the
     * session, if the param is true.  Otherwise is does nothing.  It also will
     * not allow the wizard to advance past the last step.
     *
     * @return void
     * @param bool $allow[optional] It will only actually advance the wizard if this is true or not specified
     */
    private function _nextWizard($allow = true)
    {
        if ($allow) {
            $this->currentStepKey = count($this->steps) < ($this->currentStepKey + 1) ? count($this->steps) : $this->currentStepKey + 1;
            $this->session->set('installer.currentStepKey', $this->currentStepKey);
            $this->currentStep = $this->steps[$this->currentStepKey];
            // Set any events back to default
            $this->pluginEvents = array(
                'core' => Router::$controller,
                'coreAction' => Router::$controller . '.' . $this->currentStep
            );
        }
    }
    /**
     * This method returns to the previous step in the wizard and saves it to the
     * session.  It also will not let the wizard go back past the first step.
     *
     * @return void
     */
    private function _prevWizard()
    {
        $this->currentStepKey = ($this->currentStepKey - 1) < 0 ? 0 : $this->currentStepKey - 1;
        $this->session->set('installer.currentStepKey', $this->currentStepKey);
        $this->currentStep = $this->steps[$this->currentStepKey];
        // Set any events back to default
        $this->pluginEvents = array(
            'core' => Router::$controller,
            'coreAction' => Router::$controller . '.' . $this->currentStep
        );
    }
    /**
     * This function voids the current wizard
     *
     * @return void
     */
    private function _resetWizard()
    {
        // Clean up any session vars
        $sessionVars = $this->session->get();
        foreach($sessionVars as $name => $sessionVar) {
            if (stristr($name, 'installer')) $this->session->delete($name);
        }
        // Clean up any cache configs
        $cache = Cache::instance();
        $cache->delete_tag('config_file');
        $cache->delete_tag('database_file');
        $cache->delete_tag('upload_file');
        $cache->delete_tag('locale_file');
        $cache->delete_tag('telephony_file');
        // Reset the step index
        $this->currentStepKey = 0;
        $this->session->set('installer.currentStepKey', $this->currentStepKey);
        $this->currentStep = $this->steps[$this->currentStepKey];
        // Set any events back to default
        $this->pluginEvents = array(
            'core' => Router::$controller,
            'coreAction' => Router::$controller . '.' . $this->currentStep
        );
        Kohana::log('info', 'Installer wizard reset');
    }
    /**
     * When the method/step testEnvironment creates a list of methods in this class this
     * method will assist in filtering that list to only those that start with '_check' and
     * are callable.
     *
     * @return bool True if a method is callabale and begins with '_check' otherwise false.
     * @param string $var The method name to test
     */
    private function _filterCheckMethods($var)
    {
        return strstr($var, '_check');
    }
    private function _filterInitMethods($var)
    {
        return strstr($var, 'initialize');
    }
    private function _freshInstall()
    {
        kohana::log('alert', 'Now installing, to conserve memory logging will be suppressed.');
        Kohana::config_set('core.log_threshold', 4);
        // Get the doctrine overlord
        $manager = Doctrine_Manager::getInstance();
        $conn = $manager->getCurrentConnection();
        try {
            // See if we can connect to the DB
            $conn->connect();
        }
        catch(Doctrine_Connection_Exception $e) {
            // We could connect earlier, hmmm....
            try {
                Doctrine::createDatabases();
            }
            catch(Exception $e) {
                // We cant resolve this issue without the user
                message::set('Unable to establish a connection to '
                    .$this->session->get('installer.dbName')
                    .'! <div class="error_details">' . $e->getMessage() . '</div>'
                );                
                return false;
            }
        }
        // See if the DB has any tables in it
        $tables = $conn->import->listTables();
        if (!empty($tables)) {
            // Yup, there are tables in our soon to be fresh install db, remove them
            try {
                $dsn = $conn->getOption('dsn');
                $dsn = $manager->parsePdoDsn($dsn);
                $tmpConn = $conn->getTmpConnection($dsn);
                $conn->close();
                $tmpConn->export->dropDatabase($dsn['dbname']);
                $tmpConn->export->createDatabase($dsn['dbname']);
                $manager->closeConnection($tmpConn);
                $conn->connect();
            }
            catch(Exception $e) {
                // We cant resolve this issue without the user
                message::set('Unable to recreate database '
                    .$this->session->get('installer.dbName')
                    .'! <div class="error_details">' . $e->getMessage() . '</div>'
                );                  
                return false;
            }
        }
        // Add in the core tables (only core!)
        try {
            $models = Doctrine::loadModels(APPPATH . 'models/', Doctrine::MODEL_LOADING_CONSERVATIVE);
            Doctrine::createTablesFromModels();
           // foreach($models as $model) Kohana::log('debug', 'INSTALLER::Create core table ' . $model);
        }
        catch(Exception $e) {
            message::set('Unable to create core tables!'
                .'<div class="error_details">' . $e->getMessage() . '</div>'
            );
            return false;
        }
        // For each core table see if there is an initialization routine and run it
        $initMethods = get_class_methods('Bluebox_Initialize');
        $initMethods = array_filter($initMethods, array(
            $this,
            '_filterInitMethods'
        ));
        // For each method found run it and build a results array with the result
        foreach($initMethods as $initMethod) {
            try {
                call_user_func(array(
                    'Bluebox_Initialize',
                    $initMethod
                ));
                Kohana::log('debug', 'Core table ' .$initMethod . ' complete');
                } catch(Exception $e) {
                    Kohana::log('error', 'Core table ' .$initMethod . ' failed! ' . $e->getMessage());
                    message::set('Unable to initialize core table!'
                        .'<div class="error_details">' . $e->getMessage() . '</div>'
                    );
                    return false;
                }
        }
        // Now, on to the user selected packages...
        $telDrivers = Bluebox_Installer::listPackages(Bluebox_Installer::TYPE_DRIVER);
        $currentDriver = $this->session->get('installer.tel_driver', 'none');
        if (array_key_exists($currentDriver, $telDrivers)) unset($telDrivers[$currentDriver]);
        $telDrivers = array_keys($telDrivers);
        $packages = Bluebox_Installer::listPackages(array() , true);
        $install = array();
        foreach($packages as $name => $package) {
            foreach ($telDrivers as $telDriver) {
                if (array_key_exists($telDriver, $package['required'])) {
                    unset($packages[$name]);
                    continue 2;
                }
                if ($name == $telDriver) {
                    unset($packages[$name]);
                    continue 2;
                }
            }
            // For each module get the users preference for installation or default to true
            $value = $this->session->get('installer.install_' . $package['packageName'], FALSE);
            // If the user wants to install this module then build a list so we can re-check its dependencies
            if (empty($value)) {
                $stats[$package['packageName']] = 'not_installed';
            } else {
                $install[] = $package['packageName'];
                $stats[$package['packageName']] = $package['version'];
            }
        }
        Anonymous_Statistics::addMsg($stats, 'modules', 'installer', 'modules');
        try {
            $result = Bluebox_Installer::processActions($packages, $install);
            Kohana::config_set('core.log_threshold', $this->log_threshold);
            return $result;
        } catch(Exception $e) {
            message::set('Installer Error!'
                .'<div class="error_details">' . $e->getMessage() . '</div>'
            );            
            return false;
        }
    }
    /**
     * It loops through a file (provided as an array) and looks for any var name
     * that matches a key in $config.  If found and the value of that $config element
     * differs then the line is updated.
     *
     * @return bool true if $lines was updated, otherwise false
     * @param $config array an array where the keys are the var names and the values are then values to check for
     * @param $lines array an array of lines from a file
     */
    private function _replaceConfig($config, &$lines, $convertBool = true)
    {
        $replacementMade = false;
        foreach($lines as $lineNum => $line) {
            // Look for a valid php var name followed by = or =>, then
            // figure out what is being set to and break the line into its components
            //  $result0 - the entire line
            //  $result1 - everything leading up to the var name
            //  $result2 - the var name
            //  $result3 - everything between the var and its value (whitespace, =, ect)
            //  $result4 - the value (stripped of ', ", or `)
            //  $result5 - everything after the value to the end of the line
            preg_match_all('/(.*[\'"`])([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)([\'"`][^=]*=>?[\s]*[\'"`]?)([^\'"`,;]*)([^,;]*[,;]?)$/imx', $line, $result, PREG_PATTERN_ORDER);
            // if there was not php var name found or the found var is not a key of $config, move on
            if (empty($result[2]) || !isset($config[$result[2][0]])) continue;
            // Some cases the var is set to '' which will leave $result4 unset, so handle that
            if (empty($result[4][0])) $result[4][0] = '';
            // If the var in the file is being set to what we have in $config then move on
            if ($result[4][0] == $config[$result[2][0]]) continue;
            // If we are converting a string ('false' or 'true') then do so now
            // making speciall allowances for adding or removing closing ', ", or `
            if ($convertBool) {
                if (!empty($config[$result[2][0]]) && stristr('false', $config[$result[2][0]])) {
                    $result[3][0] = rtrim($result[3][0], '\'"`');
                    $result[5][0] = ltrim($result[5][0], '\'"`');
                } else if (!empty($config[$result[2][0]]) && stristr('true', $config[$result[2][0]])) {
                    $result[3][0] = rtrim($result[3][0], '\'"`');
                    $result[5][0] = ltrim($result[5][0], '\'"`');
                } else if (!(empty($result[4][0])) && stristr('true', $result[4][0])) {
                    $result[3][0].= '\'';
                    $result[5][0] = '\'' . $result[5][0];
                } else if (!(empty($result[4][0])) && stristr('false', $result[4][0])) {
                    $result[3][0].= '\'';
                    $result[5][0] = '\'' . $result[5][0];
                }
            }
            // Because the file upload dir has php constant we need to handle it as a one-off...
            if ($result[2][0] == 'directory') {
                $result[5][0] = str_replace('\'upload\'', '', $result[5][0]);
                $result[3][0] = rtrim($result[3][0], '\'"`') . '\'';
                $result[5][0] = '\'' . ltrim($result[5][0], '\'"`');
            }
            // Strip the new line, we will add it back and this is simply for consistancy
            $result[5][0] = rtrim($result[5][0], "\n");
            // Put all the parts back together but with our new value instead of the old
            $newLine = $result[1][0] . $result[2][0] . $result[3][0] . $config[$result[2][0]] . $result[5][0];
            // Save the new line back to $lines
            $lines[$lineNum] = $newLine . "\n";
            Kohana::log('debug', 'Update config file with ' . preg_replace('/\s\s+/', ' ', $newLine));
            // Set a marker that $lines has been updated
            $replacementMade = true;
        }
        return $replacementMade;
    }
    /**
     * Takes a php_ini value and returns the bytes
     *
     * @param string $val the php_ini value to convert
     * @return int the value of $val in bytes
     */
    private function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower(substr($val, strlen($val / 1) , 1));
        if ($last == 'g') $val = $val * 1024 * 1024 * 1024;
        if ($last == 'm') $val = $val * 1024 * 1024;
        if ($last == 'k') $val = $val * 1024;
        return $val;
    }
    private function createPackageList($packages, $packageErrors = array(), $packageWarnings = array())
    {
       // what package parameters to display
        $displayParameters = array(
            'version',
            'author',
            'vendor',
            'license',
            'summary',
            'description'
        );

        $packageList = array();

        foreach ($packages as $name => $package) {
            $display = &$packageList[$name];

            $parameters = &$package;

            // Find a display name, using the package name if there is none
            // such as a plugin
            $display['displayName'] = ucfirst($parameters['displayName']);
            if (empty($display['displayName'])) {
                $display['displayName'] = ucfirst($parameters['packageName']);
            }

            // load in any other parameters we want to display about this package
            $display['displayParameters'] = array_intersect_key($parameters, array_flip($displayParameters));

            if (!empty($packageErrors[$name])) {
               $display['errors'] = implode('<br>', $packageErrors[$name]);
            }
            if (!empty($packageWarnings[$name])) {
               $display['warnings'] = implode('<br>', $packageWarnings[$name]);
            }
        }
        return $packageList;
    }
    /************************************************************************
    *					 ENVIRONMENT TEST METHODS							*
    *************************************************************************/
    /**
     * Ensures that the PHP version mets the minium requirements of Doctrine
     *
     * @return results array
     */
    private function _checkPHP()
    {
        $result = array(
            'name' => __('PHP Version') ,
            'fail_msg' => __('This requires PHP 5.2.3 or newer, this version is ') . phpversion() ,
            'pass_msg' => phpversion() ,
            'result' => version_compare(phpversion() , '5.2.3') > 0 ? true : false,
            'required' => true
        );
        // Use the pre_stats because we dont know if we have write permissions on cache/ yet!
        $preStats = $this->session->get('installer.pre_stats');
        $preStats['php_version'] = array(
            $result['pass_msg'],
            'php_version',
            'installer'
        );
        $this->session->set('installer.pre_stats', $preStats);
        return $result;
    }
    /**
     * Ensures that the PHP version mets the minium requirements of Doctrine
     *
     * @return results array
     */
    private function _checkServerSft()
    {
        $result = array(
            'name' => __('Server Software') ,
            'fail_msg' => ' ',
            'pass_msg' => $_SERVER['SERVER_SOFTWARE'],
            'result' => true,
            'required' => true
        );
        // Use the pre_stats because we dont know if we have write permissions on cache/ yet!
        $preStats = $this->session->get('installer.pre_stats');
        $preStats['server_software'] = array(
            $result['pass_msg'],
            'server_software',
            'installer'
        );
        $this->session->set('installer.pre_stats', $preStats);
        return $result;
    }
    /**
     * Ensures there are database drivers
     *
     * @return results array
     */
    private function _checkPDODrivers()
    {
        if (class_exists('PDO')) {
            $availableDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getAvailableDrivers();
            $supportedDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getSupportedDrivers();
            $drivers = array_uintersect($availableDrivers, $supportedDrivers, 'strcasecmp');
        } else {
            $drivers = array();
        }
        $result = array(
            'name' => __('Database Support') ,
            'fail_msg' => __('You don\'t have any ') . html::anchor('http://us3.php.net/manual/en/pdo.setup.php', 'PDO driver support') . __(' in your PHP install!') ,
            'pass_msg' => '',
            'result' => !empty($drivers) ,
            'required' => true
        );
        foreach($drivers as $drive) $result['pass_msg'].= $drive . ', ';
        $result['pass_msg'] = rtrim($result['pass_msg'], ', ');
        return $result;
    }
    /**
     * Currently ensures that safe mode is off
     *
     *  TODO: If safe mode is one check if it is configured properly
     *
     * @return results array
     */
    private function _checkSafeMode()
    {
        $result = array(
            'name' => __('Safe Mode ') ,
            'fail_msg' => __('This will not operate properly if safe mode is active') ,
            'pass_msg' => __('Pass') ,
            'result' => false,
            'required' => true
        );
        $safe_mode = ini_get('safe_mode');
        if (empty($safe_mode) || $safe_mode === false) $result['result'] = true;
        return $result;
    }
    /**
     * Ensures that open_basedir is null or the bluebox directory
     *
     * @return results array
     */
    private function _checkBaseDir()
    {
        $result = array(
            'name' => __('Open_BaseDir') ,
            'fail_msg' => __('Your open_basedir must be null or contain the bluebox directory') ,
            'pass_msg' => __('Pass') ,
            'result' => false,
            'required' => true
        );
        $open_basedir = ini_get('open_basedir');
        if (empty($open_basedir)) {
            $result['result'] = true;
        } else {
            $workingDir = '%' . preg_quote(getcwd() , '/') . '%im';
            if (preg_match($workingDir, $open_basedir)) $result['result'] = true;
        }
        return $result;
    }
    /**
     * Ensures that the mod_rewrite is loaded
     *
     * COUNTERPOINT: How else would we have gotten this far?  This may be redundant!
     * @todo nginx, cherokee and lighttpd support
     * @return results array
     */
    private function _checkModRewrite()
    {
        $serverSoftware = explode("/", $_SERVER['SERVER_SOFTWARE']);
        switch ($serverSoftware[0]) {
        case 'Apache':
            if (function_exists('apache_get_modules')) {
                $rewrite = in_array("mod_rewrite", apache_get_modules()) ? true : false;
            } else {
                $rewrite = false;
            }
            break;

        case 'lighttpd':
        case 'cherokee':
        case 'nginx':
        default:
            $rewrite = false;
        }
        $result = array(
            'name' => __('Mod_Rewrite') ,
            'fail_msg' => __('Mod rewite can be used ') . html::anchor('http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html', 'mod_rewrite') . __(' for clean URLs') ,
            'pass_msg' => __('Pass') ,
            'result' => $rewrite,
            'required' => false
        );
        // Use the pre_stats because we dont know if we have write permissions on cache/ yet!
        $preStats = $this->session->get('installer.pre_stats');
        $preStats['mod_rewrite'] = array(
            $result['result'],
            'mod_rewrite',
            'installer'
        );
        $this->session->set('installer.pre_stats', $preStats);
        /**
         * TODO: If COUNTERPOINT == false, check if it is actually working for this directory...
         */
        return $result;
    }
    /**
     * Ensures PCRE is loaded and configure properly for Kohana
     *
     * @return results array
     */
    private function _checkPCRE()
    {
        $result = array(
            'name' => __('PCRE UTF-8 ') ,
            'fail_msg' => html::anchor('http://php.net/pcre', 'PCRE') . __(' has not been compiled with UTF-8 support.') ,
            'pass_msg' => __('Pass') ,
            'result' => false,
            'required' => true
        );
        if (@preg_match('/^.$/u', 'ñ')) $result['result'] = true;
        else if (!@preg_match('/^\pL$/u', 'ñ')) $result['fail_msg'] = html::anchor('http://php.net/pcre', 'PCRE') . __(' has not been compiled with Unicode property support.');
        return $result;
    }
    /**
     * Ensures Reflection is loaded for Kohana
     *
     * @return results array
     */
    private function _checkReflection()
    {
        $result = array(
            'name' => __('Reflection Enabled') ,
            'fail_msg' => html::anchor('http://www.php.net/reflection', 'PHP Reflection') . __(' is either not loaded or not compiled in.') ,
            'pass_msg' => __('Pass') ,
            'result' => class_exists('ReflectionClass') ,
            'required' => true
        );
        return $result;
    }
    /**
     * Ensures Filters is loaded for Kohana
     *
     * @return results array
     */
    private function _checkFilters()
    {
        $result = array(
            'name' => __('Filters Enabled') ,
            'fail_msg' => html::anchor('http://us3.php.net/manual/en/filter.installation.php', 'Filter') . __(' extension is either not loaded or not compiled in.') ,
            'pass_msg' => __('Pass') ,
            'result' => function_exists('filter_list') ,
            'required' => true
        );
        return $result;
    }
    /**
     * Ensures Iconv is loaded for Kohana
     *
     * @return results array
     */
    private function _checkIconv()
    {
        $result = array(
            'name' => __('Iconv Extension') ,
            'fail_msg' => html::anchor('http://us.php.net/manual/en/iconv.installation.php', 'Iconv') . __(' extension is not loaded.') ,
            'pass_msg' => __('Pass') ,
            'result' => extension_loaded('iconv') ,
            'required' => true
        );
        return $result;
    }
    /**
     * Checks if the optional CURL extensions are loaded
     *
     * @return results array
     */
    private function _checkCURL()
    {
        $result = array(
            'name' => __('CURL Extension') ,
            'fail_msg' => __('The optional ') . html::anchor('http://us2.php.net/manual/en/curl.installation.php', 'cURL') . __(' extension is not loaded.') ,
            'pass_msg' => __('Pass') ,
            'result' => extension_loaded('curl') ,
            'required' => false
        );
        return $result;
    }
    /**
     * Checks if the JSON extensions are loaded
     *
     * @return results array
     */
    private function _checkJSON()
    {
        $result = array(
            'name' => __('JSON Extension') ,
            'fail_msg' => __('Unable to locate the ') . html::anchor('http://uk.php.net/manual/en/json.installation.php', 'json extenstion') ,
            'pass_msg' => __('Pass') ,
            'result' => extension_loaded('json') ,
            'required' => true
        );
        return $result;
    }
    /**
     * Ensures mbstring is not overloading php strings for Kohana
     *
     * @return results array
     */
    private function _checkMbstring()
    {
        $result = array(
            'name' => __('Mbstring Not Overloaded') ,
            'fail_msg' => html::anchor('http://php.net/mbstring', 'Mbstring') . __(' extension is overloading PHP\'s native string functions.') ,
            'pass_msg' => __('Pass') ,
            'result' => true,
            'required' => true
        );
        if (extension_loaded('mbstring')) $result['result'] = (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING == 0 ? FALSE : TRUE);
        return $result;
    }
    /**
     * Ensures Kohana will be able to figure out the URI
     *
     * @return results array
     */
    private function _checkURI()
    {
        $result = array(
            'name' => __('URI Determination') ,
            'fail_msg' => __('Neither ') . '<code>$_SERVER[\'REQUEST_URI\']</code> or <code>$_SERVER[\'PHP_SELF\']</code>' . __(' is available.') ,
            'pass_msg' => __('Pass') ,
            'result' => isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) ,
            'required' => true
        );
        return $result;
    }
    /**
     * Ensures there is enough memory for doctrine
     *
     * @return array results
     */
    private function _checkMemoryLimit()
    {
        $result = array(
            'name' => __('Memory Limit') ,
            'fail_msg' => html::anchor('http://us3.php.net/manual/en/ini.core.php#ini.memory-limit', 'Memory_limit') . __(' is set bellow the required 32MB.') ,
            'pass_msg' => __('Pass') ,
            'result' => false,
            'required' => true
        );
        $memoryLimit = ini_get('memory_limit');
        if ($memoryLimit != - 1) {
            $memoryLimit = self::return_bytes($memoryLimit);
            if ($memoryLimit >= 33554431) $result['result'] = true;
        } else {
            $result['result'] = true;
        }
        return $result;
    }
    /**
     * Ensures Kohana directory exists
     *
     * @return results array
     */
    private function _checkSystemDir()
    {
        $result = array(
            'name' => __('System Directory') ,
            'fail_msg' => __('The configured system directory does not exist or does not contain required files.') ,
            'pass_msg' => __('Pass') ,
            'result' => is_dir(SYSPATH) AND is_file(SYSPATH . 'core/Bootstrap' . EXT) ,
            'required' => true
        );
        return $result;
    }
    /**
     * Ensures Bluebox application directory exists
     *
     * @return results array
     */
    private function _checkCoreAppDir()
    {
        $result = array(
            'name' => __('Bluebox Directory') ,
            'fail_msg' => __('The configured Bluebox directory does not exist or does not contain required files.') ,
            'pass_msg' => __('Pass') ,
            'result' => is_dir(APPPATH) AND is_file(APPPATH . 'config/config' . EXT) ,
            'required' => false
        );
        return $result;
    }
    /**
     * Ensures core modules have an existing directory
     *
     * TODO: This should only work on core modules...
     *
     * @return results array
     */
    private function _checkCoreModuleDir()
    {
        $result = array(
            'name' => __('Core Modules Directory') ,
            'fail_msg' => __('The configured core module(s) could not be found') . ':<ul>',
            'pass_msg' => __('Pass') ,
            'result' => true,
            'required' => true
        );
        foreach(Kohana::config('core.modules') as $modDir) {
            if (!is_dir($modDir)) {
                if (($modPos = strrpos($modDir, '/')) !== false) $result['fail_msg'].= '<li>' . substr($modDir, $modPos + 1) . '</li>';
                $result['result'] = false;
            }
        }
        $result['fail_msg'].= '</ul>';
        return $result;
    }
    /**
     * Ensures the logs and cache directory have write permissions
     *
     * TODO: Consider checking perissions on FreeSwitch conf but it may be too early here.....hmmmm
     *
     * @return results array
     */
    private function _checkDirPermissions()
    {
        $testDirectories = array(
            '/bluebox/cache/',
            '/bluebox/logs/'
        );
        $result = array(
            'name' => __('Directory Permissions') ,
            'fail_msg' => __('The following directories do not have write permissions') . ':<ul>',
            'pass_msg' => __('Pass') ,
            'result' => true,
            'required' => true
        );
        foreach($testDirectories as $testDirectory) {
            $dir = getcwd() . $testDirectory;
            if (!filesystem::is_writable($dir)) {
                $result['fail_msg'].= '<li>' . $testDirectory . '</li>';
                $result['result'] = false;
            }
        }
        $result['fail_msg'].= '</ul>';
        return $result;
    }
}
