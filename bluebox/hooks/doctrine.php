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
*
* Karl Anderson
*
*/
/**
 * Kohana Bluebox Event Controller
 *
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
class DoctrineHook
{
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
        $dsn = $type . '://';
        if (!empty($user)) {
            $dsn.= $user;
            if (!empty($pass)) $dsn.= ':' . $pass;
            $dsn.= '@';
        }
        if (!empty($protocol)) $dsn.= $protocol . '(';
        $dsn.= $host;
        if (!empty($port)) $dsn.= ':' . $port;
        if (!empty($protocol)) $dsn.= ')';
        if (!empty($database)) {
            $dsn.= '/' . $database;
            if (!empty($dbOptions)) $dsn.= '?' . $dbOptions;
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
        $manager = Doctrine_Manager::connection($dsn, 'BlueboxDB');
        // Set up any global attributes
        $manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true); // This is needed for reserved key words like 'key' or 'value', addeds back ticks, etc...
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL); // Turn on model-level validation
        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true); // This causes behavoirs to apply to DQL as well
        //$manager->setAttribute(Doctrine::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
        // Load core models into Doctrine
        Doctrine::loadModels($modelsPath, Doctrine::MODEL_LOADING_CONSERVATIVE);
    }
}
/* http://docs.kohanaphp.com/general/events for all events */
/* start doctrine up so controller can access the models */
Event::add('system.ready', array(
    'DoctrineHook',
    'bootstrapDoctrine'
));
