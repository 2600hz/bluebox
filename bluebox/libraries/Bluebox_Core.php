<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
class Bluebox_Core
{
    /**
     * @var array Used to hold the core runtime parameters
     */
    private static $runtimeParameters = array();
    /**
     * This function can be used to set or override a modules runtime parameters.
     * The module paramters must be passed as an array of key value pairs.
     * For convience you can specify the module name (in order of precedence):
     *  >Using the moduleName parameter
     *  >As the parent key: 'moduleName' => array ('parameterName' => 'parameterValue')
     *  >In moduleParameters with the key 'name'
     * This lets you copy module parameters, import doctrine queries, or create your own
     * parameters.  However, the key 'parameters' is the only key with special meaning
     * and should be avoided (unless from the modules table).
     *
     * @param array $moduleParameters
     * @param string $moduleName
     * @return void
     */
    public static function setModuleParameters($moduleParameters, $baseDir = NULL)
    {
        foreach((array)$moduleParameters as $key => $parameters) {
            if (!empty($baseDir)) {
                $runtimeParameter = & self::$runtimeParameters['modules'][$baseDir];
                $parameters = array(
                    $key => $parameters
                );
            } else if (is_string($key)) {
                $runtimeParameter = & self::$runtimeParameters['modules'][$key];
            } else if (!empty($parameters['name'])) {
                $runtimeParameter = & self::$runtimeParameters['modules'][$parameters['basedir']];
            } else {
                continue;
            }
            if (isset($parameters['parameters']) && is_array($parameters['parameters'])) {
                if (!empty($parameters['parameters']['directory'])) {
                    $parameters['parameters']['directory'] = MODPATH . basename($parameters['parameters']['directory']);
                }
                $parameters = array_merge((array)$parameters, $parameters['parameters']);
                unset($parameters['parameters']);
            }
            $runtimeParameter = array_merge((array)$runtimeParameter, (array)$parameters);
        }
    }
    public static function getCurrentModuleParameters($parameter = NULL) {
        if (is_null($parameter)) {
            return self::getModuleParameters(Router::$controller);
        } else {
            return self::getModuleParameter(Router::$controller, $parameter);
        }
    }
    /**
     * This function can be used to get all the module parameters of a single module
     * if the name is supplied or all modules if not
     *
     * @param string $moduleName
     * @return array
     */
    public static function getModuleParameters($moduleName = NULL)
    {
        if (is_null($moduleName) && isset(self::$runtimeParameters['modules'])) {
            return self::$runtimeParameters['modules'];
        } else if (isset(self::$runtimeParameters['modules'][$moduleName])) {
            return self::$runtimeParameters['modules'][$moduleName];
        } else {
            return array();
        }
    }
    /**
     * This function can be used to get a single module parameter
     *
     * @param string $moduleName
     * @param string $parameter
     * @return mixed
     */
    public static function getModuleParameter($moduleName = NULL, $parameter = NULL)
    {
        if (isset(self::$runtimeParameters['modules'][$moduleName][$parameter])) {
            return self::$runtimeParameters['modules'][$moduleName][$parameter];
        } else {
            return NULL;
        }
    }
    /**
     * This module provides a very rudamentery method to query the modules runtime parameters
     * for selected values.  NULL is considered all
     *
     * @param mixed $findModules
     * @param mixed $getParameters
     * @return array
     */
    public static function findModulesParameters($findModules = NULL, $getParameters = NULL)
    {
        $foundModules = array();
        // Loop all the known modules
        foreach((array)self::$runtimeParameters['modules'] as $module => $parameters) {
            // Check if this meets the filter, NULL is considered all
            if (!is_null($findModules)) {
                foreach((array)$findModules as $parameter => $value) {
                    if (!isset($parameters[$parameter])) continue 2;
                    if ($parameters[$parameter] != $value) continue 2;
                }
            }
            if (is_null($getParameters)) {
                $foundModules[$module] = $parameters;
            } else if (is_string($getParameters)) {
                $foundModules[$module] = self::getModuleParameter($module, $getParameters);
            } else {
                foreach((array)$getParameters as $getParameter) {
                    $foundModules[$module][$getParameter] = self::getModuleParameter($module, $getParameter);
                }
            }
        }
        return $foundModules;
    }
    /**
     * Check if Bluebox is running the install wizard
     * 
     * @return bool
     */
    public static function is_installing()
    {   $URI = '';
        if (!empty($_SERVER['PHP_SELF'])) $URI = $_SERVER['PHP_SELF'];
        if (!empty($_SERVER['REQUEST_URI'])) $URI = $_SERVER['REQUEST_URI'];
        if (stristr($URI, 'installer') || Router::$controller == 'installer') {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Provides class auto-loading for Bluebox
     *
     * @throws  Kohana_Exception
     * @param   string  name of class
     * @return  bool
     */
    public static function auto_load($class)
    {
        if (class_exists($class, FALSE)) return TRUE;
        if (($suffix = strrpos($class, '_')) > 0) {
            // Find the class suffix
            $suffix = substr($class, $suffix + 1);
        } else {
            // No suffix
            $suffix = FALSE;
        }
        if ($suffix === 'Driver') {
            $type = 'libraries/drivers';
            $file = strtolower(str_replace('_', '/', substr($class, 0, -7)));
        } elseif ($suffix === 'Plugin') // Support added by Darren Schreiber - Bluebox project
        {
            $type = 'plugins';
            $file = strtolower(substr($class, 0, -7));
        } elseif ($suffix === 'Library') {
            $type = 'library';
            $file = strtolower(substr($class, 0, -7));
        } else {
            // Return if we don't recognize this type of class
            return false;
        }
        if ($filename = Kohana::find_file($type, $file)) {
            //kohana::log('debug', 'Require ' . str_replace(DOCROOT, '', $filename));
            require $filename;
        } else {
            // The class could not be found
            return FALSE;
        }
        if ($filename = Kohana::find_file($type, Kohana::config('core.extension_prefix') . $class)) {
            // Load the class extension
            //kohana::log('debug', 'Require ' . str_replace(DOCROOT, '', $filename));
            require $filename;
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
