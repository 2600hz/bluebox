<?php defined('SYSPATH') or die('No direct access allowed.');

class BlueboxHook {
    /**
     * Bootstrap our auto-loader to the system
     */
    public static function bootstrapCore()
    {   
        // Load in the Bluebox_Core class if it is not already,
        if (!class_exists('Bluebox_Core')) {

            if($filename = Kohana::find_file('libraries', 'Bluebox_Core')) {

                require $filename;

            } else {

                // if we cant find it then that is fatal
                kohana::log('error', 'Could not locate the core class!');
                die('Unable to locate the system core class');

            }
        }
                
        // Enable core doctrine autoloader for our models
        spl_autoload_register(array(
            'Bluebox_Core',
            'autoload'
        ));

        spl_autoload_register(array(
            'Bluebox_Core',
            'autoloadLibraries'
        ));

        Bluebox_Core::bootstrapPackages();
    }

}

Event::add('system.ready', array('BlueboxHook', 'bootstrapCore'));