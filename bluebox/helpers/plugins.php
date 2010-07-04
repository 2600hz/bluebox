<?php defined('SYSPATH') or die('No direct access allowed.');

class plugins
{
    protected static $plugins = array();

    protected static $classes = array();

    public static function construct()
    {
        // Go through all registered events and see if any of them match this url
        $classes = array();

        if (isset(self::$classes[Router::$controller]))
        {
            $classes = self::$classes[Router::$controller];
        }
        
        if (isset(self::$classes[Router::$controller . '.' . Router::$method]))
        {
            $classes = array_merge($classes, self::$classes[Router::$controller . '.' . Router::$method]);
        }

        foreach($classes as $class)
        {
            self::initialize($class);
        }
    }
    
    public static function &initialize($plugin)
    {
        // Instantiate requested object if not already done
        if (!isset(self::$plugins[$plugin]))
        {
            self::$plugins[$plugin] = new $plugin;
        }

        self::$plugins[$plugin]->pluginName = get_class(self::$plugins[$plugin]);
        
        return self::$plugins[$plugin];
    }
    
    public static function register($url, $action, array $callback)
    {
        // Make sure what we're trying to register is actually a valid plugin
        // NOTE: We do NOT use method_exists here, which would theoretically be more thorough, because it requires instantiating the object/plugin class
        // NOTE #2: In some cases we haven't loaded the class yet to be able to check this. DO NOT error out on this condition!
        if (class_exists($callback[0]) and !is_subclass_of($callback[0], 'Bluebox_Plugin'))
        {
            throw new Exception('Tried to use class ' . $callback[0] . ' as a plug-in, but it does not extend Bluebox_Plugin. All plug-ins must extend Bluebox_Plugin.');
        }

        // We allow the URL to be either an array of controller/method pair, or an actual page (like devicemanager/edit)
        if (is_array($url))
        {
            $url = $url[0] . '.' . $url[1];
        } 
        else
        {
            $url = str_replace('/', '.', $url);
        }

        // Keep track of plugin classes to initialize on specific pageloads, to allow for model preloading
        self::$classes[$url][] = $callback[0];

        //kohana::log('debug', 'Registered plugin ' .$url .' ' .$action . ' with callback ' .$callback[0] .'->' .$callback[1] .'();');

        // Register event as controller.method.action with the callback array as the callback
        Event::add($url . '.' . $action, $callback);
        
        return TRUE;
    }
    
    private static function runEvents($eventName, &$data = NULL)
    {
        kohana::log('debug', 'Running event ' . $eventName);

        // Returns of the callbacks for event specified
        $events = Event::get($eventName);

        // Loop through each event, instantiate the event object, and call the event.
        // NOTE: We do this because we rely on __get/__set methods in bluebox plugins and they must be able to use $object->
        // to reference the current controller
        $return_value = TRUE;

        foreach($events as $event)
        {
            // Share our current controller w/ the event system
            Event::$data = &$data;

            // Go get our plugin object
            $obj = & self::initialize($event[0]);

            if (method_exists($obj, $event[1]))
            {
                $return = call_user_func(array(
                    $obj,
                    $event[1]
                ));

                kohana::log('debug', 'Plugin hook ' .get_class($obj) .'::' .$event[1] .'() returned ' .($return ? 'true' : 'false'));

                // If the func doesnt have a return or its not bool, assume true
                if (is_null($return) || !is_bool($return))
                {
                    $return = TRUE;
                }

                // Bitwise AND of all the returns (if any returns false, the event will return false)
                $return_value = $return_value & $return;
            } 
            else
            {
                throw new Exception('Tried to call plugin method ' . get_class($obj) . '::' . $event[1] . '(), but no such method exists. (Event ' . $eventName . ')');
            }

            // Do this to prevent data from getting 'stuck'
            $clearData = '';
            
            Event::$data = & $clearData;
        }
        
        return $return_value;
    }
    
    public static function views(&$object, $events = array())
    {
        // If there are no keys defined for the defaults, load default values
        $events += array(
            'coreAction' => Router::$controller . '.' . Router::$method,
            'core' => Router::$controller
        );

        // Init the views array in the object
        $object->views = array();

        // Loop through each event and execute them
        $return = TRUE;

        foreach($events as $event)
        {
            // If the event is empty or set to false then skip it (this is how
            // we override execution of defaults)
            if (empty($event))
            {
                continue;
            }
            
            // Run all registered hooks for this event
            $return = $return & self::runEvents($event . '.view', $object);

        }
        
        // Move the views out of the object and into the template
        $object->template->content->views = $object->views;
        
        return (bool)$return;
    }
    
    /**
     * Execute all hooks for form saves related to this controller. Note that individual plugins should not save their own
     * data sets, rather, they add their data to the unit of work. The core application calls save.
     */
    public static function save(&$object, $events = array())
    {
        // If there are no keys defined for the defaults, load default values
        $events += array(
            'coreAction' => Router::$controller . '.' . Router::$method,
            'core' => Router::$controller
        );

        // Loop through each event and execute them
        $return = TRUE;

        foreach($events as $event)
        {
            // If the event is empty or set to false then skip it (this is how
            // we override execution of defaults)
            if (empty($event)) 
            {
                continue;
            }

            // Standardize all events on lowercase strings
            $event = strtolower($event);

            // Run all registered hooks for this event
            $return = $return & self::runEvents($event . '.save', $object);

        }
        
        return (bool)$return;
    }

    /**
     * Execute all hooks for form saves related to this controller. Note that individual plugins should not save their own
     * data sets, rather, they add their data to the unit of work. The core application calls save.
     */
    public static function delete(&$object, $events = array())
    {
        // If there are no keys defined for the defaults, load default values
        $events += array(
            'coreAction' => Router::$controller . '.' . Router::$method,
            'core' => Router::$controller
        );

        // Loop through each event and execute them
        $return = TRUE;

        foreach($events as $event)
        {
            // If the event is empty or set to false then skip it (this is how
            // we override execution of defaults)
            if (empty($event))
            {
               continue;
            }

            // Standardize all events on lowercase strings
            $event = strtolower($event);

            // Run all registered hooks for this event
            $return = $return & self::runEvents($event . '.delete', $object);
            
        }
        
        return (bool)$return;
    }
}
