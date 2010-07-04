<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * BlueboxManager_Controller.php - The one to rule them all....
 * Created on Jun 2, 2009
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Bluebox_Manager
 */
class BlueboxManager_Controller extends Bluebox_Controller
{
    protected $writable = array();
    protected $baseModel = 'Modules';
    private $tempPackagePath = '';
    public function index()
    {
        stylesheet::add('blueboxmanager', 40);
        // Get a list of the current packages
        $packages = Bluebox_Installer::listPackages();
        $posts = $this->input->post();
        if (!empty($posts['submit'])) {
            // Run through each of the submitted post vars
            foreach($posts as $post => $enabled) {
                // If the post var name doesnt have the work enabled in it move on
                if (!array_key_exists($post, $packages)) continue;
                // Get the package based on the name of the submittion
                $package = &$packages[$post];
                // If the package status is differnet then it currently is mark its action
                if ($package['installedAs']['enabled'] && empty($enabled)) {
                    $package['action'] = 'disable';
                } else if (!$package['installedAs']['enabled'] && !empty($enabled)) {
                    $package['action'] = 'enable';
                }
            }
            // Make sure these new actions arent going to end in tears
            $packages = Bluebox_Installer::checkDependencies($packages);
            // If we are cleared to process these selections
            if (empty(Bluebox_Installer::$errors)) {
                // Process the selections
                if (Bluebox_Installer::processActions($packages)) {
                    // Get the bootstrap to call all the hooks again
                    BlueboxHook::bootstrapBluebox();
                    // Let the user know we did it!
                    message::set('You changes have been saved', 'success');
                } else {
                    // Oh No! We could not process the list as it was, let the user know to scroll down for the errors
                    message::set('Unable to update modules!');
                }
            } else {
                message::set('You can not enabled or disabled the selected modules due to dependencies!');
            }
        }
        // Pass all the warnings and errors onto the view
        $packageErrors = Bluebox_Installer::$errors;
        $packageWarnings = Bluebox_Installer::$warnings;
        // We need a new list reflect the changes
        $packages = Bluebox_Installer::listPackages();
        Bluebox_Installer::installCore($packages);
        if (Kohana::config('core.repositories')) foreach (Kohana::config('core.repositories') as $repository) {
            Bluebox_Installer::checkForUpdates($packages, $repository);
        }
        // This is going to loop through and test if a package has a setting view
        // Little nasty, but in the intrest of time...it does work
        foreach($packages as $packageName => $package) {
            $eventName = 'blueboxmanager.' . $package['packageName'] . '.view';
            $events = Event::get($eventName);
            $packages[$package['packageName']]['hasSettings'] = !empty($events);
        }
        $this->createPackageList($packages, TRUE, $packageErrors, $packageWarnings);
    }
    public function settings($packageName)
    {
        $eventName = 'blueboxmanager.' . $packageName . '.view';
        $this->views = array();
        // Returns of the callbacks for system.post_controller
        $events = Event::get($eventName);
        // Loop through each event, instantiate the event object, and call the event.
        // NOTE: We do this because we rely on __get/__set methods in bluebox plugins and they must be able to use $object->
        // to reference the current controller
        foreach($events as $event) {
            // Share our current controller w/ the event system
            Event::$data = & $this;
            // Go get our plugin object
            $obj = & plugins::initialize($event[0]);
            if (method_exists($obj, $event[1])) $return_value = call_user_func(array(
                $obj,
                $event[1]
            ));
            else throw new Exception('Tried to call ' . get_class($obj) . '->' . $event[1] . ', but no such method exists. (Event ' . $eventName . ')');
            // Do this to prevent data from getting 'stuck'
            $clearData = '';
            Event::$data = & $clearData;
        }
        $this->template->content->views = $this->views;
    }
    public function repair($packageName)
    {
        $this->template->content = new View('blank');
        $packages = Bluebox_Installer::listPackages();
        $packages[$packageName]['action'] = 'repair';
        if (Bluebox_Installer::processActions($packages)) message::set('Repair of ' . $packages[$packageName]['displayName'] .' complete!', 'success');
        if (!empty(Bluebox_Installer::$warnings)) message::set($packages[$packageName]['displayName'] . ' Warning: '. arr::arrayToUL(Bluebox_Installer::$warnings[$packageName]), 'alert');
        if (!empty(Bluebox_Installer::$errors)) message::set($packages[$packageName]['displayName'] . ' Error: '. arr::arrayToUL(Bluebox_Installer::$errors[$packageName]), 'error');
        message::render(array(), array('growl' => TRUE, 'html' => FALSE));
    }
    public function uninstall($packageName)
    {
        $this->template->content = new View('blank');
        $packages = Bluebox_Installer::listPackages();
        $packages[$packageName]['action'] = 'uninstall';
        if (Bluebox_Installer::processActions($packages)) {
            message::set($packages[$packageName]['displayName'] . ' removed!', 'success');
            jquery::addQuery('#legend_' . $packageName)->parent()->slideUp();
        } else {
            if (!empty(Bluebox_Installer::$warnings)) message::set($packages[$packageName]['displayName'] . ' Warning: '.arr::arrayToUL(Bluebox_Installer::$warnings[$packageName]), 'alert');
            if (!empty(Bluebox_Installer::$errors)) message::set($packages[$packageName]['displayName'] . ' Error: '.arr::arrayToUL(Bluebox_Installer::$errors[$packageName]));
        }
        message::render(array(), array('growl' => TRUE, 'html' => FALSE));
    }
    public function packages()
    {
        stylesheet::add('blueboxmanager', 40);
        $this->template->content = new View('blueboxmanager/index');
        // Get a list of the current packages
        $packages = Bluebox_Installer::listPackages();
        $posts = $this->input->post();
        if (!empty($posts['submit'])) {
            $install = array();
            foreach($posts as $post => $process) {
                // If the post var name doesnt have the work enabled in it move on
                if (!array_key_exists($post, $packages)) continue;
                if (empty($process)) continue;
                // Get the package based on the name of the submittion
                $install[] = $post;
            }
            $packages = Bluebox_Installer::checkDependencies($packages, $install);
            // If we are cleared to process these selections
            if (empty(Bluebox_Installer::$errors)) {
                // Process the selections
                if (Bluebox_Installer::processActions($packages, $install)) {
                    // Get the bootstrap to call all the hooks again
                    BlueboxHook::bootstrapBluebox();
                    // Let the user know we did it!
                    message::set('The selected package(s) have been installed.', 'success');
                } else {
                    // Oh No! We could not process the list as it was, let the user know to scroll down for the errors
                    message::set('Unable to install packages!');
                }
            } else {
                message::set('You can not install the selected packages due to dependencies!');
            }
        }
        // Pass all the warnings and errors onto the view
        $packageErrors = Bluebox_Installer::$errors;
        $packageWarnings = Bluebox_Installer::$warnings;
        // We need a new list reflect the changes
        $packages = Bluebox_Installer::listPackages();
        $this->createPackageList($packages, FALSE, $packageErrors, $packageWarnings);
    }
    public function verify($packageName)
    {
        $this->template->content = new View('blank');
        $packages = Bluebox_Installer::listPackages();
        $packages[$packageName]['action'] = 'verify';
        if (Bluebox_Installer::processActions($packages)) message::set($packages[$packageName]['displayName'] .' passed verification!', 'success');
        if (!empty(Bluebox_Installer::$warnings)) message::set($packages[$packageName]['displayName'] . ' Warning: '.arr::arrayToUL(Bluebox_Installer::$warnings[$packageName]), 'alert');
        if (!empty(Bluebox_Installer::$errors)) message::set($packages[$packageName]['displayName'] . ' Error: '.arr::arrayToUL(Bluebox_Installer::$errors[$packageName]), 'error');
        message::render(array(), array('growl' => TRUE, 'html' => FALSE));
    }
    public function repair_all(){
        $this->template->content = new View('blank');
        $packages = Bluebox_Installer::listPackages();
        foreach($packages as $name => $package) {
            if ($package['installedAs'] == FALSE) continue;
            $packages[$name]['action'] = 'repair';
        }
        if (Bluebox_Installer::processActions($packages)) message::set('Repair of all installed packages complete!', 'success');
        if (!empty(Bluebox_Installer::$warnings)) message::set($packages[$packageName]['displayName'] . ' Warning: '. arr::arrayToUL(Bluebox_Installer::$warnings), 'alert');
        if (!empty(Bluebox_Installer::$errors)) message::set($packages[$packageName]['displayName'] . ' Error: '. arr::arrayToUL(Bluebox_Installer::$errors), 'error');
        message::render(array(), array('growl' => TRUE, 'html' => FALSE));
    }
    /**
     *
     * TODO: THIS IS AN EMBARRASSINGLY SIMPLE UPDATE SYSTEM!
     * TODO: The last repository found with an updated version of the package
     *       in question will always win
     * TODO: There is no method to select which version to move to
     * TODO: There is no way to downgrade
     * TODO: There is no auth system (all repos are 'trusted')
     *
     * @param string The name of the package to update
     */
    public function update($packageName) {
        $this->template->content = new View('blank');

        // Get a list of packages on this system
        $packages = Bluebox_Installer::listPackages(array(), TRUE);
        Bluebox_Installer::installCore($packages);
        // sanity check that we are working with a valid package
        if (empty($packages[$packageName])) {
            message::set('No such package ' .$packageName);
            url::redirect(Router::$controller);
            return FALSE;
        }

        // scan each configured repository for updates
        foreach (kohana::config('core.repositories') as $repository) {
            kohana::log('debug', 'Check repo ' .$repository);
            Bluebox_Installer::checkForUpdates($packages, $repository);
        }
        $package = $packages[$packageName];

        // get the URL of the repo we will be using, and determine the file
        // name of the update we will be downloading
        $updateURL = $package['updateURL'];
        $updateName = parse_url($updateURL, PHP_URL_PATH);
        $updateName = basename($updateName);
        $cacheDir = APPPATH .'cache/'. md5($updateURL) .'/';

        // sanity check ourselfs
        if (empty($package['updateAvaliable'])) {
            message::set('No update avaliable for ' .$package['displayName']);
            url::redirect(Router::$controller);
            return FALSE;
        } else if (empty($updateURL)) {
            message::set('Unable to determine update URL for ' .$package['displayName']);
            url::redirect(Router::$controller);
            return FALSE;
        } else if (empty($updateName)) {
            message::set('Unable to determine update file name for ' .$package['displayName']);
            url::redirect(Router::$controller);
            return FALSE;
        }

        if (!filesystem::createDirectory($cacheDir)) {
            message::set('Failed to create update cache for ' .$package['displayName']);
            url::redirect(Router::$controller);
            return FALSE;
        }

        // read in a file from the repo bit by bit saving it to the cache as
        // we go...
        $destination = @fopen($cacheDir .$updateName, 'w');
        $source = @fopen($updateURL, 'r');

        // sanity check our file handles
        if (!$destination) {
            message::set('Unable to cache update for ' .$package['displayName']);
            url::redirect(Router::$controller);
            return FALSE;
        } else if (!$source) {
            message::set('Could not download update ' .$updateURL);
            url::redirect(Router::$controller);
            return FALSE;
        }

        kohana::log('debug', 'Downloading update from ' .$updateURL);
        stream_set_timeout($source, 15);
        while ($a=fread($source,1024)) {
            if (fwrite($destination,$a) === FALSE) {
                message::set('Error while downloading and saving update for ' .$package['displayName']);
                fclose($source);
                fclose($destination);
                url::redirect(Router::$controller);
                return FALSE;
            }
        }

        fclose($source);
        fclose($destination);

        // open the zip archive (our update)
        set_error_handler(array('BlueboxManager_Controller', 'exception_handler'));
        try {
            $zip = new ZipArchive;
            if ($zip->open($cacheDir .$updateName) === FALSE) {
                message::set('Could not open downloaded update for ' .$package['displayName']);
                url::redirect(Router::$controller);
                return FALSE;
            }

            if ((filesystem::createDirectory($cacheDir .'migration') && $zip->extractTo($cacheDir .'migration/')) === FALSE) {
                message::set('Could not extract downloaded update for ' .$package['displayName']);
                url::redirect(Router::$controller);
                return FALSE;
            }
        } catch (Exception $e) {
            set_error_handler(array('Kohana', 'exception_handler'));
            message::set('Decompression error: ' .$e->getMessage());
            url::redirect(Router::$controller);
            return FALSE;
        }

        $zip->close();

        if ($packageName == 'core') {
            $this->updateCore($cacheDir);
            url::redirect(Router::$controller);
            return FALSE;
        }

        kohana::log('debug', 'Update imported, removing current verision');

        if (!empty($package['directory'])) {
            try {
                $currentModPath = MODPATH .$package['directory'];

                filesystem::createDirectory($cacheDir .'current');
                if(!filesystem::copy($currentModPath, $cacheDir .'current/')) {
                    message::set('Failed to backup current module');
                    url::redirect(Router::$controller);
                    return FALSE;
                }

                filesystem::delete($currentModPath);
            } catch (Exception $e) {
                set_error_handler(array('Kohana', 'exception_handler'));
                message::set('Current package removal error: ' .$e->getMessage());
                url::redirect(Router::$controller);
                return FALSE;
            }
        }

        if(!filesystem::copy($cacheDir .'migration/', MODPATH)) {
            message::set('Failed to import upgraded module');
            url::redirect(Router::$controller);
            return FALSE;
        }

        set_error_handler(array('Kohana', 'exception_handler'));

        $isCurrentlyEnabled = $package['installedAs']['enabled'];

        $configureFile = Bluebox_Installer::$configurations[$package['configureClass']];

        unset(Bluebox_Installer::$configurations[$package['configureClass']]);

        $declaredBefore = get_declared_classes();
        require($configureFile);
        $declaredAfter = get_declared_classes();

        if (count($declaredBefore) != count($declaredAfter)) {
            $foundClass = end($declaredAfter);

            Bluebox_Installer::$configurations[$foundClass] = $configureFile;
        }

        $packages = Bluebox_Installer::listPackages();
        $packages[$packageName]['action'] = 'upgrade';
        $packages[$packageName]['default'] = $isCurrentlyEnabled;

        Bluebox_Installer::processActions($packages);

        message::set('Update of ' .$packages[$packageName]['displayName'] .' complete!', 'success');

        url::redirect(Router::$controller);
    }
    public function updateCore($cacheDir) {
        $migration = $cacheDir .'migration/';

        $updateFiles = filesystem::directoryToArray($migration, TRUE, array('multidimensional' => FALSE));

        try {
            foreach ($updateFiles as $relative => $abs) {
                $destination = $cacheDir .'current/' .dirname($relative) .'/';

                if (!filesystem::createDirectory($destination)) {
                    message::set('Failed to create backup folder for core prior to update');
                    return FALSE;
                }

                if (!is_file(APPPATH .$relative) && !is_link(APPPATH .$relative)) {
                    continue;
                }
                if(!filesystem::copy(APPPATH .$relative, $cacheDir .'current/' .$relative)) {
                    message::set('Failed to backup core file prior to update');
                    return FALSE;
                }
            }

            foreach ($updateFiles as $relative => $abs) {
                $destination = $cacheDir .'current/' .dirname($relative) .'/';

                if (is_file(APPPATH .$relative) && is_link(APPPATH .$relative)) {
                    if (!filesystem::delete(APPPATH .$relative, FALSE)) {
                        message::set('Failed to remove core file prior to update');
                        return FALSE;
                    }
                }

                if(!filesystem::copy($abs, APPPATH .$relative)) {
                    message::set('Failed to update core file');
                    return FALSE;
                }
            }

        } catch (Exception $e) {
                message::set('Error during core update: ' .$e->getMessage());
                return FALSE;
        }
        message::set('Update of Core complete!', 'success');
    }

    public static function exception_handler($errno, $errstr, $errfile, $errline, $errcontext) {
        throw new Exception($errstr, $errno);
    }

    public function delete()
    {
    }

    public function upload()
    {
        if ($this->input->post()) {
        }
    }

    public function maintenance()
    {
        if ($this->input->post('submit') == 'Regenerate Now') {
            $devices = Doctrine::getTable('SipDevice')->findAll();
            foreach ($devices as $device) {
                Bluebox_Record::setBaseSaveObject($device);

                foreach ($device->getReferences() as $reference) {
                    echo get_class($reference);
                    Telephony::set($reference);
                }
            }
            Bluebox_Record::setBaseSaveObject(NULL);

            $numbers = Doctrine::getTable('Number')->findAll();
            foreach ($numbers as $number) {
                Bluebox_Record::setBaseSaveObject($number);
                Telephony::set($number);
            }
            Bluebox_Record::setBaseSaveObject(NULL);

            if (class_exists('Trunk')) {
                $trunks = Doctrine::getTable('Trunk')->findAll();
                foreach ($trunks as $trunk) {
                    Telephony::set($trunk);
                }
            }

            if (class_exists('SipInterface')) {
                $interfaces = Doctrine::getTable('SipInterface')->findAll();
                foreach ($interfaces as $interface) {
                    Telephony::set($interface);
                }
            }

            Telephony::save();

            Telephony::commit();

            message::set('Successfully regenerated core configs.');
        }
    }

    private function createPackageList($packages, $installed = TRUE, $packageErrors = array(), $packageWarnings = array())
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
            // if this package is not installed skip it
            if (empty($installed)) {
                if ($package['installedAs'] != FALSE) continue;
            } else {
                if ($package['installedAs'] == FALSE) continue;
            }

            $display = &$packageList[$name];

            if (empty($installed)) {
                $parameters = &$package;

                $display['allowInstall'] = TRUE;
                $display['allowVerify'] = TRUE;
            } else {
                $parameters = &$package['installedAs']['parameters'];
                $display['allowRepair'] = TRUE;

                // see if we should allow this module to be uninstalled
                if ($parameters['canBeRemoved'] && $parameters['canBeDisabled']) {
                    $display['allowUninstall'] = TRUE;
                } else {
                    $display['allowUninstall'] = FALSE;
                }
                // see if we can disable this moduel
                $display['allowDisable'] = $parameters['canBeDisabled'];

                // create a convience pointer to the installed parameters
                $display['enabled'] = $package['installedAs']['enabled'] ? TRUE : FALSE;
            }

            if (!empty($package['updateAvaliable'])) {
                $display['updateAvaliable'] = $package['updateAvaliable'];
                $display['updateURL'] = $package['updateURL'];
            } else {
                $display['updateAvaliable'] = FALSE;
                $display['updateURL'] = NULL;
            }

            // Find a display name, using the package name if there is none
            // such as a plugin
            $display['displayName'] = ucfirst($parameters['displayName']);
            if (empty($display['displayName'])) {
                $display['displayName'] = ucfirst($parameters['packageName']);
            }

            // load in any other parameters we want to display about this package
            $display['displayParameters'] = array_intersect_key($parameters, array_flip($displayParameters));

            if (!empty($packageErrors[$name])) {
               $display['errors'] = arr::arrayToUL($packageErrors[$name]);
            }
            if (!empty($packageWarnings[$name])) {
               $display['warnings'] = arr::arrayToUL($packageWarnings[$name]);
            }
        }
        $this->view->packageList = $packageList;
    }
    private function validate()
    {
    }
}
