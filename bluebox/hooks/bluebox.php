<?php defined('SYSPATH') or die('No direct access allowed.');
/*
* Bluebox Modular Telephony Software Library / Application
*
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
*
* Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
* express or implied. See the License for the specific language governing rights and limitations under the License.
*
* The Original Code is Bluebox Telephony Configuration API and GUI Framework.
* The Original Developer is the Initial Developer.
* The Initial Developer of the Original Code is Darren Schreiber
* All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
*
* Contributor(s):
* K Anderson
*
*
*/
/**
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
define('SUCCESS', 1);
define('PARTIAL', 2);
define('FAILED', -1);
class BlueboxHook
{
    public static function bootstrapBluebox()
    {
        if (!class_exists('Bluebox_Core')) {
            if($filename = Kohana::find_file('libraries', 'Bluebox_Core')) {
                require $filename;
            } else {
                // The class could not be found
                return FALSE;
            }
        }
        spl_autoload_register(array(
            'Bluebox_Core',
            'auto_load'
        ));
        // We cant let this run during the install process and the Router has yet to execute...
        if (Bluebox_Core::is_installing()) {
            $dbModules = glob(MODPATH . '*', GLOB_ONLYDIR);
            // Prepare a list of all modules on this system so we can scan their hook
            $modules = array_unique(array_merge(Kohana::config('core.modules') , $dbModules));
        } else {
            // Add the moules from the Db Modules table to the core.modules config variable. Ignore dupes.
            $dbModules = array();
            try {
                // Load up all installed modules parameters into the runtime parameters
                $q = Doctrine_Query::create()->select('m.basedir, m.name, m.enabled, m.parameters')->from('Module m');
                Bluebox_Core::setModuleParameters($q->execute(NULL, Doctrine::HYDRATE_ARRAY));
                // Find all enabled modules
                $dbModules = Bluebox_Core::findModulesParameters(array('enabled' => true), 'directory');
            }
            catch(Exception $e) {
                // Do nothing - no biggie
                $dbModules = array();
            }
            // Merge modules configured via the Db into the core module list
            $modules = array_unique(array_merge(Kohana::config('core.modules') , $dbModules));
            // Load models
            foreach($modules as $module) {
                if (is_dir($module . '/models')) {
                    // Note that with MODEL_LOADING_CONSERVATIVE set, the model isn't really loaded until first requested
                    Doctrine::loadModels($module . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
                }
            }
        }
        Kohana::config_set('core.modules', $modules);
        // Load hooks only for modules in the DB, if hooks are enabled (assume other hooks are already loaded)
        if (Kohana::config('core.enable_hooks') === TRUE) {
            foreach($dbModules as $module) {
                if (is_dir($module . '/hooks')) {
                    // Since we're running late, we need to go grab the hook files again (sad but true)
                    $hooks = Kohana::list_files('hooks', TRUE, $module . '/hooks');
                    foreach($hooks as $file) {
                        // Load the hook
                        include_once $file;
                    }
                }
            }
        }
    }
}
// start doctrine up so controllers can access the models
Event::add('system.ready', array(
    'DoctrineHook',
    'bootstrapDoctrine'
));
// when the system starts run these actions
Event::add('system.ready', array(
    'BlueboxHook',
    'bootstrapBluebox'
));