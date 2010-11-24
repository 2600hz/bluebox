<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Installer
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Installer_Controller extends Bluebox_Controller
{
    /**
     * @var bool $autoloadUser do not allow our parent class to load the user as they dont exist yet!
     */
    public $autoloadUser = FALSE;

    /**
     * @var string $template name of the installer base template
     */
    public $template = 'skins/installer/';

    /**
     * @var array The absolute minimum packages that will be installed
     */
    public $minimumPackages = array(
        'core',
        'packagemanager',
        'maintenance',

        // System Modules
        'accountmanager',
        'usermanager',
        'rosetta',
        'errorreporter',
        'dashboard'
    );

    /**
     *
     * @var int $log_threshold the log_threshold starts at 0 and this is what to set to post-install
     */
    public $log_threshold = 4;

    /**
     * @var array $steps List of functions to process for install (order is important)
     */
    protected $steps = array(
        'welcome',
        'configure',
        'createAdmin',
        'telephony',
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
        if (!Kohana::config('config.installer_enabled'))
        {
            throw new Exception('The installer has been administratively disabled. (You can re-enable it in Bluebox/config/config.php)');
        }

        Kohana::config_set('core.site_domain', Bluebox_Installer::guess_site_domain());

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
        if ($this->currentStepKey > 1 && Kohana::config('core.log_threshold') == 0)
        {
            Kohana::config_set('core.log_threshold', $this->log_threshold);
        }

        // This is the default list of steps to run, modified to work with the wizard
        $this->pluginEvents = array(
            'core' => Router::$controller,
            'coreAction' => Router::$controller . '.' . $this->currentStep
        );

        if ($this->currentStep != 'finalize')
        {
            $this->_loadAllModules();
        }
        else
        {
            Bluebox_Core::bootstrapPackages(TRUE);
        }
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
        if (!empty($returns))
        {
            // This test makes sure that the form being submitted has a matching token,
            // since the token changes every time the form is rendered if the tokens dont
            // match then it must be a refresh....
            $testToken = $this->session->get('installer.formToken', FALSE);

            if (empty($returns['form_token']) || $returns['form_token'] != $testToken)
            {
                if (isset($returns['next']))
                {
                    unset($returns['next']);
                }

                if (isset($returns['prev']))
                {
                    unset($returns['prev']);
                }
            }

            // Save all the submits from this form
            $ignoreReturns = array(
                'next',
                'prev',
                'license',
                'formToken'
            );

            foreach($returns as $name => $return)
            {
                if (!in_array($name, $ignoreReturns))
                {
                    $this->session->set('installer.' . $name, $return);
                }
            }

            $driver = $this->session->get('installer.tel_driver');

            if (!empty($driver))
            {
                // Add an event for this telephony driver exclusively
                $this->pluginEvents['driver'] = Router::$controller . '.' . $this->currentStep . '.' . $driver;
            }

            // See if the form was submitted with the next button
            if (!empty($returns['next']))
            {
                // If the user wants to continue check for the existance of a method called process{stepName}
                if (method_exists($this, 'process' . ucfirst($this->currentStep)))
                {
                    // It the process exists go to the next step on if this step returns true
                    $proceed = call_user_func(array(
                        $this,
                        'process' . ucfirst($this->currentStep)
                    ));
                } 
                else
                {
                    $proceed = TRUE;
                }

                // Run all registered save hooks for this installer.step
                $proceed = $proceed & plugins::save($this, $this->pluginEvents);

                // If the user wants to go back on step who are we to stop them?
                $this->_nextWizard($proceed);
            } 
            else if (!empty($returns['prev']))
            {
                $this->_prevWizard();
            }
        }
        // Lets generate a unique token that the form must respond with so on refresh
        // it doesnt progress unexpectedly
        $formToken = strtoupper(md5(uniqid(rand() , TRUE)));

        $this->session->set('installer.formToken', $formToken);

        $this->template->formToken = $formToken;

        // Default to allow them to go to the back a step if the are not on the first.
        $this->template->allowPrev = $this->currentStepKey > 0 ? TRUE : FALSE;

        // By default they can always continue (but a step may disable this ability!)
        $this->template->allowNext = TRUE;

        // Attempt to render this step
        try
        {
            $subview = call_user_func(array(
                $this,
                $this->currentStep
            ));
            
            // Set the step view in the main template
            $this->template->content = $subview;
            
            // Run all registered view hooks for this installer.step
            plugins::views($this, $this->pluginEvents);

            $this->template->views = $this->template->content->views;
        }
        catch(Exception $e)
        {
            $this->template->title = 'INSTALLATION ERROR';

            $this->template->allowPrev = FALSE;

            $this->template->allowNext = FALSE;

            $subview  = '<h2 class="error">ERROR: Can not execute step ' . $this->currentStep .', wizard terminated!</h2>';

            $subview .= '<small class="error">' .$e->getMessage() .'</small>';

            $this->session->destroy();

            // Set the step view in the main template
            $this->template->content = $subview;
        }
    }
    
    /**
     * This attempts to uncomment the index_page in config.php if mod_rewrite is not
     * on or not allowed.
     *
     * @return void
     */
    public function fixModRewrite()
    {
        Kohana::config_set('core.site_domain', Bluebox_Installer::guess_site_domain());

        $indexPage = Kohana::config('core.index_page');

        if (!empty($indexPage))
        {
            url::redirect('/installer');
        }

        Kohana::config_set('core.index_page', 'index.php');

        // Get the current config.php file
        if ($files = Kohana::find_file('config', 'config'))
        {
            $file = @file(end($files));
        }

        // Make sure we were sucessfull
        if (empty($file))
        {
            // Use the preLog because we dont know if we have write permissions on logs/ yet!
            $preLog = $this->session->get('installer.pre_log');

            $preLog['error'][] = 'Could not locate or read config.php during mod_rewrite fix!';

            $this->session->set('installer.pre_log', $preLog);

            url::redirect('/index.php/installer?config_file=config');
        }

        foreach($file as $num => $line)
        {
            preg_match('/.*[\'"`]([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\'"`].*$/imx', $line, $result);

            if (!empty($result[1]) && strstr($result[1], 'index_page'))
            {
                $file[$num] = ltrim($line, '/#;');
            }
        }
        
        $file = implode('', $file);

        // If we got the file then we must have made changes, attempt to save it back
        // but if there is an error doing so have the user do it
        if (@file_put_contents(end($files) , $file) === FALSE)
        {
            // Use the preLog because we dont know if we have write permissions on logs/ yet!
            $_GET['config_file'] = 'config';

            $this->viewCache($file);

            $this->template->allowNext = TRUE;

            return FALSE;
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

        $this->template->title = 'Manual ' .ucfirst($cache_file) .' Config Update';

        $this->template->formToken = ' ';

        $this->template->views = array();

        if (empty($configFile))
        {
            $configFile = 'File ' .$cache_file .'.php does not exist or can not be read!';
        }

        if (empty($configContents))
        {
            $cache = Cache::instance();

            $configContents = $cache->find($cache_file . '_file');

            // This orders the cache by creation so we show the newest
            ksort($configContents, SORT_NUMERIC);

            $configContents = end($configContents);
        }

        if (!empty($configContents))
        {
            message::set('Please manually replace the contents of ' . $cache_file . '.php!<div>You can also change the permissions so the installer can write to it.</div>', 'info');

            $this->template->content = '<div>' . $configFile . '</div>';

            $this->template->content.= '<textarea style="width: 100%; height: 300px;">' . $configContents . '</textarea>';
        } 
        else
        {
            $this->template->content = '';
            
            message::set('Oops, I can\'t find the new content for ' . $cache_file . '.php! Sorry guess you are on your own...');
        }

        $this->template->allowPrev = FALSE;

        $this->template->allowNext = FALSE;
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
        if ($files = Kohana::find_file('config', $config))
        {
            foreach ($files as $file)
            {
                if (strstr($file, SYSPATH))
                {
                    continue;
                }

                $lines = @file($file);

                // Compare what we where given to what is in the file and replace what differs
                if (self::_replaceConfig($configMap, $lines))
                {
                    $lines = implode('', $lines);

                    kohana::log('debug', 'writting bluebox config contents back to -> ' .$file);

                    // If we got the file then we must have made changes, attempt to save it back
                    // but if there is an error doing so have the user do it
                    if (@file_put_contents($file, $lines) === FALSE)
                    {
                        $cache->set(time(), $lines, $config. '_file');

                        message::set('Unable to write to ' .$config .'.php, please manualy replace it with ' .html::anchor('installer/viewCache?config_file=' . $config, 'this!', array(
                            'target' => '_blank'
                        )));

                        return FALSE;
                    }
                }
            }
        }
        else
        {
            message::set('Could not locate or read ' . $config . '.php!');

            return FALSE;
        }
        
        return TRUE;
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

        $this->template->title = 'Welcome to Bluebox ' .Bluebox_Controller::$version .' Setup Wizard';

        // Load any previous settings
        $subview->acceptLicense = $this->session->get('installer.acceptLicense');

        // Based on the selected language append a file name to the license directory
        $license_file = APPPATH . 'views/installer/EN_US_LICENSE.TXT';

        // If that license exists set it to be shown in the subview
        if (file_exists($license_file))
        {
            $subview->license = file_get_contents($license_file);
        } 
        else
        {
            // If the license doesnt exist direct the user to view it at some website
            $subview->license = __('The license file could not be located.  Please read the license at ') . 'http://www.mozilla.org/MPL/MPL-1.1.html';

            message::set('You are still legally accepting the license so please ensure you read it!', 'alert');
        }


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

        foreach($checkMethods as $checkMethod)
        {
            // Call the function and get the returned result
            $result = call_user_func(array(
                $this,
                $checkMethod
            ));

            // Check the result to track if we fail either the required or optional dependencies
            if (!$result['result'])
            {
                if ($result['required'])
                {
                    $hasErrors = TRUE;
                }
                else
                {
                    $hasWarnings = TRUE;
                }

                // Build the final results array so the user can see the overal status
                $subview->results[] = $result;
            }
        }

        if (!empty($hasErrors))
        {
            message::set('Installation can not continue until dependiencies have been met! Refresh to test again.');

            // Set a session var so we can tell if we should be allowed to continue without re-running all the methods
            $this->session->set('installer.environmentTestFailed', TRUE);
        }

        if (!empty($hasWarnings))
        {
            message::set('Installation can continue but functionality may be reduced! Refresh to test again.', 'alert');
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
        $valid = TRUE;

        $acceptLicense = $this->session->get('installer.acceptLicense', FALSE);

        if (empty($acceptLicense))
        {
            message::set('Please accept the license to continue!');
            
            $valid = FALSE;
        }

        $environmentGood = $this->session->get('installer.environmentTestFailed', FALSE) ? FALSE : TRUE;

        if (!$environmentGood)
        {
            $valid = FALSE;
        }
        else
        {
            // Untill this point we couldnt write to the log because we may not have had permissions in logs/
            // so write out everything that was stored now that we know we can.
            Kohana::config_set('core.log_threshold', $this->log_threshold);
        }

        return $valid;
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
        foreach($drivers as $driver)
        {
            $driversPDO[$driver] = $driver;
        }
        
        $defaultType = Kohana::config('database.default.connection.type', reset($driversPDO));
        
        if (!in_array($defaultType, $driversPDO))
        {
            $defaultType = reset($driversPDO);
        }
        
        $subview->dbType = $this->session->get('installer.dbType', $defaultType);

        $subview->dbTypes = $driversPDO;

        if (stristr($subview->dbType, 'sqlite'))
        {
            $subview->dbPathName = $this->session->get('installer.dbPathName', Kohana::config('database.default.connection.host'));

            $subview->dbHostName = $this->session->get('installer.dbHostName', '127.0.0.1');
        } 
        else
        {
            $subview->dbHostName = $this->session->get('installer.dbHostName', Kohana::config('database.default.connection.host'));
            
            $subview->dbPathName = $this->session->get('installer.dbPathName');
        }

        $port = Kohana::config('database.default.connection.port');
        
        if (empty($port))
        {
            $port = '';
        }
        
        $subview->dbPortSelection = $this->session->get('installer.dbPortSelection', $port);

        $subview->dbUserName = $this->session->get('installer.dbUserName', Kohana::config('database.default.connection.user'));

        $subview->dbUserPwd = $this->session->get('installer.dbUserPwd', Kohana::config('database.default.connection.pass'));

        $subview->dbName = $this->session->get('installer.dbName', Kohana::config('database.default.connection.database'));

        //$subview->dbPersistent = $this->session->get('installer.dbPersistent', Kohana::config('database.default.persistent'));

        // Passback or setup the site domain
        $subview->autoURI = $this->session->get('installer.siteDomain', '/' .trim(Kohana::config('core.site_domain'), '/'));

        // Passback or setup the upload dir
        $subview->uploadDir = $this->session->get('installer.uploadDir', Kohana::config('upload.directory'));

        // Get the default timezone
        $subview->defaultTimeZone = $this->session->get('installer.defaultTimeZone');
        
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
        if (($log_threshold = Kohana::config('core.log_threshold')) == 0) 
        {
            $log_threshold = $this->log_threshold;
        }

        // This array maps the database returns to the database config file
        $databaseOptions = array(
            'type' => $this->session->get('installer.dbType'),
            'host' => $this->session->get('installer.dbHostName'),
            'port' => $this->session->get('installer.dbPortSelection'),
            'user' => $this->session->get('installer.dbUserName'),
            'pass' => $this->session->get('installer.dbUserPwd'),
            'database' => $this->session->get('installer.dbName')
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
        if (stristr($databaseOptions['type'], 'sqlite'))
        {
            $databaseOptions['host'] = $this->session->get('installer.dbPathName');

            if (!file_exists($databaseOptions['host']))
            {
                message::set('SQLite path does not exist or can not be read!');
                
                return FALSE;
            }
        }
        
        // Ensure we have everything we need
        if (empty($databaseOptions['type']) || empty($databaseOptions['host']) || empty($databaseOptions['database']))
        {
            message::set('Minimum database configuration not provided!');

            return FALSE;
        }
        
        // If the user wants to use a custom port then set that up
        if (empty($databaseOptions['port']))
        {
            $databaseOptions['port'] = 'FALSE';
        }
        
        // anonymous statistics need to be a quoted bool for the config parser
        if (empty($configOptions['anonymous_statistics']))
        {
            $configOptions['anonymous_statistics'] = 'FALSE';
        } 
        else
        {
            $configOptions['anonymous_statistics'] = 'TRUE';
        }
        
        // database persistents need to be a quoted bool for the config parser
        if (empty($databaseOptions['persistent']))
        {
            $databaseOptions['persistent'] = 'FALSE';
        } 
        else
        {
            $databaseOptions['persistent'] = 'TRUE';
        }
        
        // Get the doctrine overlord
        $manager = Doctrine_Manager::getInstance();

        // Close all current DB connections
        foreach($manager as $conn)
        {
            $manager->closeConnection($conn);
        }

        foreach($databaseOptions as $dbOption => $value)
        {
            Kohana::config_set('database.default.connection.' . $dbOption, $value);
        }

        // Re-initialize Doctrine
        call_user_func(array(
            'DoctrineHook',
            'bootstrapDoctrine'
        ) , FALSE);

        try
        {
            // See if we can connect to the db
            $manager->getCurrentConnection()->connect();
        }
        catch(Doctrine_Connection_Exception $e)
        {
            // If we can not connect to the existing db and we are in upgrade mode
            // then error out
            $installMode = $this->session->get('installer.installMode', 'install');

            if ($installMode != 'install')
            {
                // We cant resolve this issue without the user
                message::set('Unable to establish a connection to '
                    .$databaseOptions['database']
                    .'! <div class="error_details">' . $e->getMessage() . '</div>'
                );
                
                return FALSE;
            }

            try
            {
                // See if we can fix this problem by creating the DB, but only if we are in install mode!
                // If we are in upgrade then the user provided the wrong DB info...
                $response = $manager->createDatabases();

                /**
                 * TODO: Doctrine 1.2 does not seem to throw bad credentials for postgres
                 *       ....REPORT THIS TO DOCTRINE!
                 */
                if (!is_string($response['BlueboxDB']))
                {
                    $conn = $manager->getCurrentConnection();
                    
                    $conn->rethrowException($response['BlueboxDB'], $conn);
                }
            }
            catch(Exception $e)
            {
                // We cant resolve this issue without the user
                message::set('Unable to establish a connection to ' .$databaseOptions['type'] .'! <div class="error_details">' . $e->getMessage() . '</div>');
                
                return FALSE;
            }

            $this->session->set('installer.ensureInstall', $databaseOptions['type']);
        }

        $ensureInstall = $this->session->get('installer.ensureInstall', 'none');
        
        // If this db exists, we are preforming a fresh install, and the user hasn't been warned.
        // Force them to go around again accepting this fact!
        if ($ensureInstall != $databaseOptions['type'])
        {
            message::set('The existing database will be permanently erased if you continue!');

            message::set('Click continue again to proceed...', 'alert');

            // This session var lets the user continue the second time around (after the warning)
            $this->session->set('installer.ensureInstall', $databaseOptions['type']);
            
            return FALSE;
        }

        // Write $configOptions to config.php
        if (!self::updateConfig($configOptions, 'config'))
        {
            return FALSE;
        }

        // Write $localeOptions to locale.php
        if (!self::updateConfig($localeOptions, 'locale'))
        {
            return FALSE;
        }
        
        // Write $databaseOptions to database.php
        if (!self::updateConfig($databaseOptions, 'database'))
        {
            return FALSE;
        }
        
        // Write $uploadOptions to upload.php
        if (!self::updateConfig($uploadOptions, 'upload')) 
        {
            return FALSE;
        }

        // Go ahead and enable or disable anonymous stats based on the user, because we are about to try to use it
        if ($configOptions['anonymous_statistics'] == 'FALSE')
        {
            //Anonymous_Statistics::clear();

            Kohana::config_set('core.anonymous_statistics', FALSE);
        } 
        else
        {
            Kohana::config_set('core.anonymous_statistics', TRUE);

            // If the user has opted into anonymous_statistics then write out what we have stored in session previously
            $preStats = $this->session->get('installer.pre_stats');

            if (!empty($preStats) && Kohana::config('core.anonymous_statistics'))
            {
                foreach($preStats as $id => $preStat)
                {
                    //Anonymous_Statistics::addMsg($preStat[0], $preStat[1], $preStat[2], $id);
                }

                $this->session->delete('installer.pre_stats');
            }

            //Anonymous_Statistics::addMsg($databaseOptions['type'], 'db_type', 'installer', 'db_type');
        }

        return TRUE;
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
        $valid = TRUE;

        if (!valid::email($this->session->get('installer.adminEmailAddress')))
        {
            message::set('You must enter a valid email address to continue!');

            $valid = FALSE;
        } 
        elseif (strlen($this->session->get('installer.adminPassword')) < 1)
        {
            message::set('You need to set a password!');

            $valid = FALSE;
        } 
        elseif ($this->session->get('installer.adminPassword') != $this->session->get('installer.adminConfirmPassword'))
        {
            message::set('Passwords do not match!');
            
            $valid = FALSE;
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

        $drivers = array(
            'none' => 'None'
        );

        $packages = Package_Catalog::getCatalog();

        foreach($packages as $package)
        {
            if ($package['type'] != Package_Manager::TYPE_DRIVER)
            {
                continue;
            }

            $this->session->set('installer.install_' . $package['packageName'], FALSE);

            $drivers[$package['packageName']] = $package['displayName'];
        }

        // Get the driver configured in telephony.php
        $defaultDriver = strtolower(Kohana::config('telephony.driver'));

        // If the driver in telephony doesnt exist on this system then just default to the first driver
        if (!isset($drivers[$defaultDriver]))
        {
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

        if ($driver != 'none')
        {
            $this->session->set('installer.install_' . $driver, TRUE);

            // Add an event for this telephony driver exclusively
            $this->pluginEvents['driver'] = Router::$controller . '.' . $this->currentStep . '.' . $driver;
        } 
        else
        {
            // Set the driver name to none in telephony.php if there is no driver
            if (!Installer_Controller::updateConfig(array(
                'driver' => 'none'
            ) , 'telephony')) return FALSE;
        }

        //Anonymous_Statistics::addMsg($driver, 'tel_driver', 'installer', 'tel_driver');
        
        return TRUE;
    }

    /**
     * This step installs stuff
     *
     * @return subview
     */
    private function doInstall()
    {
        $subview = new View('installer/doInstall');

        if ($this->session->get('installer.installMode', 'install') == 'install')
        {
            $this->template->title = __('Installation');

            $subview->process = __('Install');
        } 
        else
        {
            $this->template->title = ucfirst(__('upgrade'));
            
            $subview->process = __('upgrade');
        }
        
        return $subview;
    }
    
    private function processDoInstall()
    {
        if ($this->session->get('installer.installMode') == 'install')
        {
            $result = self::_freshInstall();
        } 
        else
        {
            $result = self::_existingUpgrade();
        }

        if ($result != FALSE && empty(Bluebox_Installer::$errors))
        {
            Kohana::log('info', 'Install wizard completed successfully');

            return TRUE;
        } 
        else
        {
            if (empty($this->template->error))
            {
                message::set('Install failed with errors!'
                    .'<div>'
                    .arr::arrayToUL(Bluebox_Installer::$errors, array(), array('class' => 'error_details', 'style' => 'text-align:left;'))
                    .'</div>'
                );
            }

            return FALSE;
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

        $this->template->allowPrev = FALSE;

        $this->template->allowNext = FALSE;
 
        // Force a login of the master/admin user for the remainder of the install
        Auth::instance()->force_login($this->session->get('installer.adminEmailAddress'));

        users::isUserAuthentic();

        users::getCurrentUser();

        $created = $this->session->get('Bluebox_installer.created');

        Bluebox_Tenant::generateDevice($created['accountId'], $created['userId']);

        if (Session::instance()->get('installer.samples', FALSE))
        {
            $sampleUsers = array(
                array(
                    'first' => 'Peter',
                    'last' => 'Gibbons',
                    'username' => 'peter@initech.com',
                    'password' => inflector::generatePassword(),
                    'user_type' => User::TYPE_NORMAL_USER
                ),
                array(
                    'first' => 'Michael',
                    'last' => 'Bolton',
                    'username' => 'michael@initech.com',
                    'password' => inflector::generatePassword(),
                    'user_type' => User::TYPE_NORMAL_USER
                ),
                array(
                    'first' => 'Samir',
                    'last' => 'Nagheenanajar',
                    'username' => 'samir@initech.com',
                    'password' => inflector::generatePassword(),
                    'user_type' => User::TYPE_NORMAL_USER
                ),
                array(
                    'first' => 'Bill',
                    'last' => 'Lumbergh',
                    'username' => 'bill@initech.com',
                    'password' => inflector::generatePassword(),
                    'user_type' => User::TYPE_NORMAL_USER
                ),
                array(
                    'first' => 'Milton',
                    'last' => 'Waddams',
                    'username' => 'milton@initech.com',
                    'password' => inflector::generatePassword(),
                    'user_type' => User::TYPE_NORMAL_USER
                )
            );

            foreach ($sampleUsers as $sampleUser)
            {
                $userId = Bluebox_Tenant::initializeUser($created['accountId'], $created['locationId'], $sampleUser);

                Bluebox_Tenant::generateDevice($created['accountId'], $userId);
            }
        }

        if ($this->session->get('installer.tel_driver') == 'freeswitch')
        {
            Event::run('freeswitch.reload.xml');

            Event::run('freeswitch.reload.acl');

            Event::run('freeswitch.reload.sofia');
        }
        
        self::_resetWizard();

        $this->session->delete('Bluebox_message');

        // Disable the installer after a successful installtion
        self::updateConfig(array('installer_enabled' => 'FALSE'), 'config');

        Kohana::log('info', 'Installer wizard terminated');
        
        return $subview;
    }

    /************************************************************************
    *				 INSTALL WIZARD SUPPORT METHODS							*
    *************************************************************************/
    private function _loadAllModules()
    {
        $loadList = glob(MODPATH .'*', GLOB_MARK);

        foreach($loadList as $key => $module)
        {
            if (strstr($module, '_'))
            {
                unset($loadList[$key]);
            }            
        }

        $loadedModules = Kohana::config('core.modules');

        $systemModules = array_unique(array_merge($loadedModules, $loadList));

        Kohana::config_set('core.modules', $systemModules);
        
        foreach ($loadList as $packageDir)
        {
            // Load hooks only for modules in the DB, if hooks are enabled
            if (Kohana::config('core.enable_hooks') === TRUE)
            {
                if (is_dir($packageDir .'/hooks'))
                {
                    // Since we're running late, we need to go grab
                    // the hook files again (sad but true)
                    $hooks = Kohana::list_files('hooks', TRUE, $packageDir .'/hooks');

                    foreach($hooks as $file)
                    {
                        // Load the hook
                        include_once $file;
                    }
                }
            }
        }
    }

    /**
     * This method advances to the next step in the wizard and saves it to the
     * session, if the param is true.  Otherwise is does nothing.  It also will
     * not allow the wizard to advance past the last step.
     *
     * @return void
     * @param bool $allow[optional] It will only actually advance the wizard if this is true or not specified
     */
    private function _nextWizard($allow = TRUE)
    {
        if ($allow)
        {
            $this->currentStepKey = count($this->steps) < ($this->currentStepKey + 1) ? count($this->steps) : $this->currentStepKey + 1;

            $this->session->set('installer.currentStepKey', $this->currentStepKey);

            $this->currentStep = $this->steps[$this->currentStepKey];

            // Set any events back to default
            $this->pluginEvents = array(
                'core' => Router::$controller,
                'coreAction' => Router::$controller . '.' . $this->currentStep
            );

            $driver = $this->session->get('installer.tel_driver');

            if (!empty($driver)) 
            {
                // Add an event for this telephony driver exclusively
                $this->pluginEvents['driver'] = Router::$controller . '.' . $this->currentStep . '.' . $driver;
            }

            if ($this->currentStep == 'finalize')
            {
                url::redirect('/installer');
            }
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

        $driver = $this->session->get('installer.tel_driver');

        if (!empty($driver))
        {
            // Add an event for this telephony driver exclusively
            $this->pluginEvents['driver'] = Router::$controller . '.' . $this->currentStep . '.' . $driver;
        }
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

        foreach($sessionVars as $name => $sessionVar)
        {
            if (stristr($name, 'installer'))
            {
                $this->session->delete($name);
            }
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
        // Get the doctrine overlord
        $manager = Doctrine_Manager::getInstance();

        $conn = $manager->getCurrentConnection();

        try
        {
            // See if we can connect to the DB
            $conn->connect();
        }
        catch(Doctrine_Connection_Exception $e) 
        {
            // We could connect earlier, hmmm....
            try
            {
                Doctrine::createDatabases();
            }
            catch(Exception $e)
            {
                // We cant resolve this issue without the user
                message::set('Unable to establish a connection to '
                    .$this->session->get('installer.dbName')
                    .'! <div class="error_details">' . $e->getMessage() . '</div>'
                );

                return FALSE;
            }
        }

        // See if the DB has any tables in it
        $tables = $conn->import->listTables();

        if (!empty($tables))
        {
            // Yup, there are tables in our soon to be fresh install db, remove them
            try
            {
                $dsn = $conn->getOption('dsn');

                $dsn = $manager->parsePdoDsn($dsn);

                $tmpConn = $conn->getTmpConnection($dsn);

                $conn->close();

                $tmpConn->export->dropDatabase($dsn['dbname']);

                $tmpConn->export->createDatabase($dsn['dbname']);

                $manager->closeConnection($tmpConn);

                $conn->connect();
            }
            catch(Exception $e)
            {
                // We cant resolve this issue without the user
                message::set('Unable to recreate database '
                    .$this->session->get('installer.dbName')
                    .'! <div class="error_details">' . $e->getMessage() . '</div>'
                );
                
                return FALSE;
            }
        }
        
        $driver = $this->session->get('installer.tel_driver', 'none');

        kohana::log('debug', 'Installer running for driver ' .$driver);

        $packages = $this->session->get('installer.default_packages', array());

        $packages = arr::merge($this->minimumPackages, $packages, array($driver));
        
        try
        {
            $transaction = Package_Transaction::beginTransaction();

            foreach ($packages as $package)
            {
                try
                {
                    $identifier = Package_Catalog::getFirstAvaliablePackage($package);
                    
                    $transaction->install($identifier);
                }
                catch(Exception $e)
                {
                    kohana::log('error', 'Error during initial install package selection: ' .$e->getMessage());
                }

            }

            $transaction->commit();

            Session::instance()->set('Bluebox_installer.created', Bluebox_Tenant::$created);
            
            return TRUE;
        }
        catch(Exception $e)
        {
            message::set('Installer Error!'
                .'<div class="error_details">' . $e->getMessage() . '</div>'
            );

            return FALSE;
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
    private function _replaceConfig($config, &$lines, $convertBool = TRUE)
    {
        $replacementMade = FALSE;

        foreach($lines as $lineNum => $line)
        {
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
            if (empty($result[2]) || !isset($config[$result[2][0]]))
            {
                continue;
            }
            
            // Some cases the var is set to '' which will leave $result4 unset, so handle that
            if (empty($result[4][0]))
            {
                $result[4][0] = '';
            }
            
            // If the var in the file is being set to what we have in $config then move on
            if ($result[4][0] == $config[$result[2][0]])
            {
                continue;
            }
            
            // If we are converting a string ('false' or 'true') then do so now
            // making speciall allowances for adding or removing closing ', ", or `
            if ($convertBool)
            {
                if (!empty($config[$result[2][0]]) && stristr('false', $config[$result[2][0]]))
                {
                    $result[3][0] = rtrim($result[3][0], '\'"`');

                    $result[5][0] = ltrim($result[5][0], '\'"`');
                } 
                else if (!empty($config[$result[2][0]]) && stristr('true', $config[$result[2][0]]))
                {
                    $result[3][0] = rtrim($result[3][0], '\'"`');

                    $result[5][0] = ltrim($result[5][0], '\'"`');
                } 
                else if (!(empty($result[4][0])) && stristr('true', $result[4][0]))
                {
                    $result[3][0].= '\'';

                    $result[5][0] = '\'' . $result[5][0];
                } 
                else if (!(empty($result[4][0])) && stristr('false', $result[4][0]))
                {
                    $result[3][0].= '\'';
                    
                    $result[5][0] = '\'' . $result[5][0];
                }
            }
            // Because the file upload dir has php constant we need to handle it as a one-off...
            if ($result[2][0] == 'directory')
            {
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
            $replacementMade = TRUE;
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

        foreach ($packages as $name => $package)
        {
            $display = &$packageList[$name];

            $parameters = &$package;

            // Find a display name, using the package name if there is none
            // such as a plugin
            $display['displayName'] = ucfirst($parameters['displayName']);

            if (empty($display['displayName']))
            {
                $display['displayName'] = ucfirst($parameters['packageName']);
            }

            // load in any other parameters we want to display about this package
            $display['displayParameters'] = array_intersect_key($parameters, array_flip($displayParameters));

            if (!empty($packageErrors[$name]))
            {
               $display['errors'] = arr::arrayToUL($packageErrors[$name]);
            }

            if (!empty($packageWarnings[$name]))
            {
               $display['warnings'] = arr::arrayToUL($packageWarnings[$name]);
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
            'result' => version_compare(phpversion() , '5.2.3') > 0 ? TRUE : FALSE,
            'required' => TRUE
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
            'result' => TRUE,
            'required' => TRUE
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
        if (class_exists('PDO'))
        {
            $availableDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getAvailableDrivers();

            $supportedDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getSupportedDrivers();

            $drivers = array_uintersect($availableDrivers, $supportedDrivers, 'strcasecmp');
        } 
        else
        {
            $drivers = array();
        }
        
        $result = array(
            'name' => __('Database Support') ,
            'fail_msg' => __('You don\'t have any ') . html::anchor('http://us3.php.net/manual/en/pdo.setup.php', 'PDO driver support') . __(' in your PHP install!') ,
            'pass_msg' => '',
            'result' => !empty($drivers) ,
            'required' => TRUE
        );
        
        foreach($drivers as $drive)
        {
            $result['pass_msg'].= $drive . ', ';
        }
        
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
            'result' => FALSE,
            'required' => TRUE
        );
        
        $safe_mode = ini_get('safe_mode');

        if (empty($safe_mode) || $safe_mode === FALSE)
        {
            $result['result'] = TRUE;
        }

        return $result;
    }
    
    /**
     * Ensures that open_basedir is null or the Bluebox directory
     *
     * @return results array
     */
    private function _checkBaseDir()
    {
        $result = array(
            'name' => __('Open_BaseDir') ,
            'fail_msg' => __('Your open_basedir must be null or contain the Bluebox directory') ,
            'pass_msg' => __('Pass') ,
            'result' => FALSE,
            'required' => TRUE
        );

        $open_basedir = ini_get('open_basedir');

        if (empty($open_basedir))
        {
            $result['result'] = TRUE;
        } 
        else
        {
            $workingDir = '%' . preg_quote(getcwd() , '/') . '%im';
            
            if (preg_match($workingDir, $open_basedir))
            {
                $result['result'] = TRUE;
            }
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

        switch ($serverSoftware[0]) 
        {
            case 'Apache':
                if (function_exists('apache_get_modules'))
                {
                    $rewrite = in_array("mod_rewrite", apache_get_modules()) ? TRUE : FALSE;
                }
                else
                {
                    $rewrite = FALSE;
                }

                break;

            case 'lighttpd':
            case 'cherokee':
            case 'nginx':
            default:
                $rewrite = FALSE;
        }

        $result = array(
            'name' => __('Mod_Rewrite') ,
            'fail_msg' => __('Mod rewite can be used ') . html::anchor('http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html', 'mod_rewrite') . __(' for clean URLs') ,
            'pass_msg' => __('Pass') ,
            'result' => $rewrite,
            'required' => FALSE
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
            'result' => FALSE,
            'required' => TRUE
        );

        if (@preg_match('/^.$/u', 'ñ'))
        {
            $result['result'] = TRUE;
        }
        else if (!@preg_match('/^\pL$/u', 'ñ'))
        {
            $result['fail_msg'] = html::anchor('http://php.net/pcre', 'PCRE') . __(' has not been compiled with Unicode property support.');
        }
        
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
            'required' => TRUE
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
            'required' => TRUE
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
            'required' => TRUE
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
            'required' => FALSE
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
            'required' => TRUE
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
            'result' => TRUE,
            'required' => TRUE
        );

        if (extension_loaded('mbstring'))
        {
            $result['result'] = (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING == 0 ? FALSE : TRUE);
        }
        
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
            'required' => TRUE
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
            'result' => FALSE,
            'required' => TRUE
        );

        $memoryLimit = ini_get('memory_limit');

        if ($memoryLimit != - 1)
        {
            $memoryLimit = self::return_bytes($memoryLimit);

            if ($memoryLimit >= 33554431)
            {
                $result['result'] = TRUE;
            }
        } 
        else
        {
            $result['result'] = TRUE;
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
            'required' => TRUE
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
            'required' => FALSE
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
            'result' => TRUE,
            'required' => TRUE
        );

        foreach(Kohana::config('core.modules') as $modDir)
        {
            if (!is_dir($modDir))
            {
                if (($modPos = strrpos($modDir, '/')) !== FALSE)
                {
                    $result['fail_msg'].= '<li>' . substr($modDir, $modPos + 1) . '</li>';
                }

                $result['result'] = FALSE;
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
            'result' => TRUE,
            'required' => TRUE
        );

        foreach($testDirectories as $testDirectory)
        {
            $dir = getcwd() . $testDirectory;

            if (!filesystem::is_writable($dir))
            {
                $result['fail_msg'].= '<li>' .ltrim($testDirectory, '/') . '</li>';

                $result['result'] = FALSE;
            }
        }

        $result['fail_msg'].= '</ul>';

        return $result;
    }
}
