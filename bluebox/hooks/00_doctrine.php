<?php defined('SYSPATH') or die('No direct access allowed.');

class DoctrineHook
{
    const DB_KEY = 'BlueboxDB';

    public static function bootstrapDoctrine()
    {
        $type = Kohana::config('database.default.connection.type');

        $user = Kohana::config('database.default.connection.user');

        $pass = Kohana::config('database.default.connection.pass');

        $host = Kohana::config('database.default.connection.host');

        $port = Kohana::config('database.default.connection.port');

        $protocol = Kohana::config('database.default.connection.protocol');

        $database = Kohana::config('database.default.connection.database');

        $socket = Kohana::config('database.default.connection.socket');

        $dbOptions = Kohana::config('database.default.connection.db_options');

        // Determine the DSN from the config options
        $dsn = $type . '://';

        if (!empty($user)) {

            $dsn.= $user;

            if (!empty($pass)) {

                $dsn.= ':' . $pass;

            }

            $dsn.= '@';
        }

        if (!empty($protocol)) {

            $dsn.= $protocol . '(';

        }

        $dsn.= $host;

        if (!empty($port)) {

            $dsn.= ':' . $port;

        }

        if (!empty($protocol)) {

            $dsn.= ')';

        }

        if (!empty($database)) {

            $dsn.= '/' . $database;

            if (!empty($dbOptions)) {

                $dsn.= '?' . $dbOptions;
                
            }
            
        }

        // Setup the paths to all our libraries and models
        $doctrinePath = dirname(__FILE__) . "/../libraries/doctrine/lib";

        $modelsPath = dirname(__FILE__) . "/../models";

        set_include_path($doctrinePath . PATH_SEPARATOR . get_include_path());

        // Get the doctrine overlord
        require_once ('Doctrine.php');

        // Enable core doctrine autoloader for core libraries
        spl_autoload_register(array(
            'Doctrine',
            'autoload'
        ));
        
        // Enable model-based autoloader for our own models
        spl_autoload_register(array(
            'Doctrine',
            'modelsAutoload'
        ));

        // Awaken the overlord...
        $manager = Doctrine_Manager::connection($dsn, self::DB_KEY);

       // This is needed for reserved key words like 'key' or 'value', addeds back ticks, etc...
        $manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true); 

        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);

        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

        // Turn on model-level validation
        $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL); 

        // This causes behavoirs to apply to DQL as well
        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
        //
        //$manager->setAttribute(Doctrine::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
        
        // Load core models into Doctrine
        Doctrine::loadModels($modelsPath, Doctrine::MODEL_LOADING_CONSERVATIVE);
    }
}

// start doctrine up so controller can access the models
Event::add('system.ready', array(
    'DoctrineHook',
    'bootstrapDoctrine'
));
