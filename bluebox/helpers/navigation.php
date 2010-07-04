<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Bluebox navigation class. Handles menu bar and other navigation tasks.
 *
 * @license LGPL
 * @author K Anderson
 * @package Bluebox
 * @subpackage Core
 *
 */
class navigation
{
    public static $currentSubMenu = NULL;

    public static $currentBaseUrl = NULL;
    
    public static function getNavTree($restrictToDepth = FALSE)
    {
        // get the navStructures varibale for all enabled modules
        $moduleParameters = Bluebox_Core::findModulesParameters(array(
            'type' => Bluebox_Installer::TYPE_MODULE,
            'enabled' => TRUE
        ), 'navStructures');

        // loop each enabled module with any navStructures
        $navTree = array();
        foreach($moduleParameters as $module => $navStructure) {
            // loop the navStructures
            foreach((array)$navStructure as $parameters) {
                // Set a pointer in a multidemensional array limited by $restrictToDepth
                $navigation = array();
                $navPt = & $navigation;
                $depth = 0;
                if (!empty($parameters['navBranch'])) {
                    $navBranch = explode('/', $parameters['navBranch']);
                    $navBranch = array_filter($navBranch);
                    foreach($navBranch as $depth => $branch) {
                        if (is_int($restrictToDepth) && $depth > $restrictToDepth) {
                            break;
                        }
                        $navPt = & $navPt[__($branch)];
                    }
                }
                // if the navStructure does not branch this deep fill to $restriceToDepth
                if (is_int($restrictToDepth) && $depth < $restrictToDepth) {
                    for ($depth; $depth < $restrictToDepth; $depth++) {
                        $navPt = & $navPt['UNSET'];
                    }
                }
                // Mark this as a LEAF
                $navPt = & $navPt[];

                // force the expected keys to have some default value if missing
                $parameters += array(
                    'currentNavItem' => NULL,
                    'module' => $module
                );

                if (self::atModule($parameters['navURL'])) {
                    $parameters['currentNavItem'] = TRUE;
                    self::$currentSubMenu = $parameters['navSubmenu'];
                    self::$currentBaseUrl = $parameters['navURL'];
                } else {
                    $parameters['currentNavItem'] = FALSE;
                }

                // Save these parameters to the pointer
                $navPt = $parameters;
                // merge the pointer with the result array
                $navTree = array_merge_recursive($navTree, $navigation);
            }
        }
        return $navTree;
    }
    /**
     * This function will attempt to find a icon starting from the most specific location
     * and working down a skins defualt.  You may specify the intended size as small, medium,
     * or large.  If restrictToSkin is true then we will not consider icons in the modules,
     * or if it is an array then that is considered a list of icons to get from the skin only.
     *
     * For example the 32x32 sip interface icon for the bluebox skin would render the following
     *    search path (unless restricted to skin)
     *
     * sipinterface/assets/img/icons/32x32/bluebox/sipinterface.png
     * sipinterface/assets/img/icons/32x32/sipinterface.png
     * skins/bluebox/assets/img/icons/32x32/sipinterface.png
     * skins/bluebox/assets/img/icons/32x32/default.png
     * 
     */
    public static function getNavIcon($navStructure, $size = 'medium', $options = array())
    {
        // init our options
        if (!is_array($options)) {
            $options = array(
                'restrictToSkin' => FALSE
            );
        }
        $options += array (
            'restrictToSkin' => FALSE,
            'allowSkinSpecific' => TRUE
        );
        extract($options);
        
        // init our array, get the module name, and clean up the skin name
        $lookIn = array();
        $name = $navStructure['module'];
        $skin = str_replace('skins/', '', skins::getSkin());

        // make sure we are dealing with our defualt sizes
        switch ($size) {
            case 'small':
                $size = '16x16';
                break;
            case 'medium':
                $size = '32x32';
                break;
            case 'large':
                $size = '48x48';
                break;
            default:
                $size = '32x32';
                break;  
        }

        // basepath for searching within a module
        $basePath = MODPATH . $name . '/assets/img/icons/' .$size .'/';
        $baseURL = url::base() . $name . '/assets/img/icons/' .$size .'/';
        // see if the module provides an icon for this skin
        if (empty($allowSkinSpecific) || (is_array($allowSkinSpecific) && array_key_exists($name, $allowSkinSpecific))) {
            $lookIn[$baseURL . $skin . $name . '.png'] = $basePath . $skin . $name . '.png';
        }

        // see if the module provides a default icon of the correct size
        if (empty($restrictToSkin) || (is_array($restrictToSkin) && array_key_exists($name, $restrictToSkin))) {
            $lookIn[$baseURL . $name . '.png' ] = $basePath . $name . '.png';
        }

        // basepath for searching the skin
        $basePath = DOCROOT . 'skins/' . $skin . 'assets/img/icons/' .$size .'/';
        $baseURL = url::base(). 'skins/' . $skin . 'assets/img/icons/' .$size .'/';
        $lookIn[$baseURL . $name . '.png'] = $basePath . $name . '.png';
        $lookIn[$baseURL . 'default.png'] = $basePath . 'default.png';

        // look for this icon
        foreach ($lookIn as $url => $path){
            if (file_exists($path)) {
                return $url;
            }
        }

        return FALSE;
    }
    public static function getNavClasses($navStructure)
    {
        if (empty($navStructure['currentNavItem'])) {
            $class = 'navItem nav' . ucfirst($navStructure['module']);
        } else {
            $class = 'navItem navCurrentItem nav' . ucfirst($navStructure['module']);
        }
        return $class;
    }
    /**
     * This function returns the currentBaseUrl set by getNavTree.  If it
     * has not been set yet then it will be NULL
     *
     * @return string
     */
    public static function getCurrentBaseUrl() {
        return self::$currentBaseUrl;
    }
    /**
     * This function returns true if the $url matches our current module
     * If no url is provided then it attempts to use the currentBaseUrl set by
     * getNavTree.  If it doesnt know it returns NULL
     *
     * @param string $url
     * @return bool
     */
    public static function atModule($url = NULL)
    {
        // if we where not give a url then use the currentBaseUrl
        if (is_null($url)) {
            $url = self::$currentBaseUrl;
        }

        // if we still dont have a url then there is nothing else to do
        if (is_null($url)) {
            return NULL;
        }

        // see if the controller and method we match where we are
        if (self::getController() == self::getController($url)) {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * This function returns true if the the $url matches our current location
     * If no url is provided then it attempts to use the currentBaseUrl set by
     * getNavTree.  If it doesnt know it returns NULL
     *
     * @param string $url
     * @return bool
     */
    public static function atUrl($url = NULL)
    {
        // if we where not give a url then use the currentBaseUrl
        if (is_null($url)) {
            $url = self::$currentBaseUrl;
        }

        // if we still dont have a url then there is nothing else to do
        if (is_null($url)) {
            return NULL;
        }

        // see if the controller and method we match where we are
        if (self::getController() == self::getController($url)) {
        if (self::getMethod() == self::getMethod($url)) {
            return TRUE;
        }}
        return FALSE;

    }
    /**
     * This function attempts to find and return any submenu of the
     * current module
     *
     * @return array
     */
    public static function getCurrentSubMenu()
    {
        if (!is_null(self::$currentSubMenu)) {
            $submenu = self::$currentSubMenu;

        } else {
            $submenu = self::getSubMenu(self::getController(), TRUE);
        }
        return $submenu;
    }
    /**
     * This function attempts to find and return any submenus of the
     * provided module
     *
     * @param string $url
     * @return bool
     */
    public static function getSubMenu($module, $currentOnly = FALSE)
    {
        $submenus = array();
        $navStructures = Bluebox_Core::getModuleParameter($module, 'navStructures');
        foreach ((array)$navStructures as $navStructure) {
            if (isset($navStructure['navSubmenu'])) {
                if (!empty($currentOnly)) {
                    if (self::atModule($navStructure['navURL'])) {
                        return $navStructure['navSubmenu'];
                    } else {
                        continue;
                    }
                }
                $submenus[$navStructure['navURL']] = $navStructure['navSubmenu'];
            }
        }
        return $submenus;
    }
    /**
     * Get the controller.  If no uri is supplied then the current controller is retrieved but
     * be carefull if you request it to early it will be NULL.  If you want to know if it is
     * valid check if event system.routing has run.
     *
     * @return string
     * @param object $uri[optional]
     */
    public static function getController($uri = null)
    {
        if (empty($uri)) return strtolower(Router::$controller);
        else return strtolower(self::parseURI($uri, 'controller'));
    }
    /**
     * Get the method.  If no uri is supplied then the current method is retrieved but
     * be carefull if you request it to early it will be NULL.  If you want to know if it is
     * valid check if event system.routing has run.
     *
     * @return string
     * @param object $uri[optional]
     */
    public static function getMethod($uri = null)
    {
        if (empty($uri)) return strtolower(Router::$method);
        else return strtolower(self::parseURI($uri, 'method'));
    }
    /**
     * This function extracts the controller or method from an abritrary uri
     *
     * @return string|array depending on component
     * @param object $uri
     * @param object $component[optional] valid string[controller|method] A empty string causes return to be an array with keys controller and method
     */
    public static function parseURI($uri = '', $component = NULL)
    {
        // Remove the site from the uri and then explode on '/'
        if (substr($uri,0,strlen(url::site()))==url::site()) {
                $uri=substr($uri,strlen(url::site()));
	}
        $uri = str_replace(url::site() , '', $uri);
        $result['parts'] = explode('/', $uri);
        // Remove any empty values
        $result['parts'] = array_filter($result['parts']);
        // Pass the first exploded part through standardizeString and set it as the controller name
        $string = reset($result['parts']);
        $result['controller'] = self::standardizeString($string , 'welcome');
        // Pass the next exploded part through standardizeString and set it as the method name
        $string = next($result['parts']);
        $result['method'] = self::standardizeString($string , 'index');
        // Determine which value to return based on $component
        switch (strtolower($component)) {
        case 'controller':
            return $result['controller'];
        case 'method':
            return $result['method'];
        default:
            return $result;
        }
    }
    /**
     * This function ensures that strings are safe for
     * comparision. If you assign, pass, or return an undefined
     * variable by reference, it will get created
     *
     * @return string
     * @param string $string
     * @param string $default[optional]
     */
    public static function standardizeString(&$string, $default = '')
    {
        // Set to default if string is unset or empty
        if (empty($string)) $string = $default;
        // Strip any extentions
        $string = explode('.', $string);
        // Return string in all lowercase
        return strtolower(reset($string));
    }
}
