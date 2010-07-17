<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Bluebox/Libraries/Core
 * @author     Darren Schreiber
 * @license    Mozilla Public License (MPL)
 */
class Bluebox_Core
{
    public static function bootstrapPackages()
    {

        if (Bluebox_Installer::is_installing())
        {
            return TRUE;
        }

        $installedPackages = Doctrine::getTable('Package')->findByStatus(Package_Manager::STATUS_INSTALLED);

        if (empty($installedPackages))
        {
            return FALSE;
        }

        $loadList = $navigation = array();

        foreach($installedPackages as $package)
        {
            $packageDir = DOCROOT.$package['basedir'];

            $loadList[$package['name']] = $packageDir;

            if (is_dir($packageDir .'/models'))
            {
                // Note that with MODEL_LOADING_CONSERVATIVE set, the model isn't really loaded until first requested
                Doctrine::loadModels($packageDir .'/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
            }

            if (empty($package['navigation']))
            {
                continue;
            }

            $navigation[$package['name']] = $package['navigation'];
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

        navigation::bootstrap($navigation);
    }

    /**
     * Provides class auto-loading for Bluebox
     *
     * @throws  Kohana_Exception
     * @param   string  name of class
     * @return  bool
     */
    public static function autoload($class)
    {
        if (class_exists($class, FALSE))
        {
            return TRUE;
        }

        if (($suffix = strrpos($class, '_')) > 0)
        {
            // Find the class suffix
            $suffix = substr($class, $suffix + 1);
        } 
        else
        {
            // No suffix
            $suffix = FALSE;
        }

        if ($suffix === 'Driver')
        {
            $type = 'libraries/drivers';

            $file = strtolower(str_replace('_', '/', substr($class, 0, -7)));
        } 
        elseif ($suffix === 'Plugin')
        {
            $type = 'plugins';

            $file = strtolower(substr($class, 0, -7));
        } 
        elseif ($suffix === 'Library')
        {
            $type = 'library';

            $file = strtolower(substr($class, 0, -7));
        } 
        else
        {
            // Return if we don't recognize this type of class
            return false;
        }

        if ($filename = Kohana::find_file($type, $file))
        {
            //kohana::log('debug', 'Require ' . str_replace(DOCROOT, '', $filename));
            require $filename;
        } 
        else
        {
            // The class could not be found
            return FALSE;
        }

        if ($filename = Kohana::find_file($type, Kohana::config('core.extension_prefix') . $class))
        {
            // Load the class extension
            //kohana::log('debug', 'Require ' . str_replace(DOCROOT, '', $filename));
            require $filename;
            
            return TRUE;
        } 
        else
        {
            return FALSE;
        }
    }
 
    public static function autoloadLibraries($class)
    {
        if (class_exists($class, FALSE))
        {
            return TRUE;
        }

        // Convert underscores to directories
        $class = str_replace('_', '/', $class);

        // Go through all known Kohana paths and look for libraries/class
        foreach (Kohana::include_paths() as $path)
        {
            if (is_file($path.'libraries/' . $class . '.php'))
            {
                require_once $path.'libraries/'.$class.'.php';

                return TRUE;
            }
        }

        return FALSE;
    }
}