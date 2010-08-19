<?php defined('SYSPATH') or die('No direct access allowed.');

class OdbcManager
{
    /*
     * @TODO Look into kohana code to see if port lookup can be replaced
     *
     */
    public static function create($module , $description = '', $connection = NULL)
    {
        if(self::isEnabled($module))
        {
            return TRUE;
        }

        if(is_null($connection))
        {
            $connection = array();
        }

        $defaults = Kohana::config('database.default.connection');

        $odbc = new Odbc();

        // Put our defaults in first
        $odbc->fromArray($defaults);

        // Clobber our defaults (intentionally) with user settings, if any
        $odbc->fromArray($connection);

        /* look up the port by type since its a required field. */
        $odbc->port = self::lookupPort($odbc->type);

        $odbc->dsn_name = 'bluebox';

        $odbc->module = $module;

        $odbc->xpath = $xpath;

        $odbc->description = $description;

        $odbc->enabled = TRUE;

        try
        {
            $odbc->save();
        } 
        catch(Doctrine_Validator_Exception $e)
        {
            // will never get here
        }

        return TRUE;
    }

    public static function disable($module)
    {
        $odbc = Doctrine::getTable('Odbc')->findOneByModule($module);

        if($odbc)
        {
            $odbc->enabled = FALSE;

            $odbc->save();
        } 
        else
        {
            message::set("Failed to disable ODBC module $module");
        }
    }

    public static function lookupPort($type)
    {
        switch($type)
        {
            case 'mysql':
                return 3306;

                break;

            case 'pgsql':
                return 5432;

                break;

            default:
                message::set("Unable to resolve port for $type");
        }
    }

    public function dsnSelector($name, $default = NULL)
    {
        $connections = array(0 => 'None');

        $odbc = Doctrine::getTable('Odbc')->findAll();

        foreach($odbc as $dsn)
        {
            $connections[$dsn->odbc_id] = $dsn->dsn_name . ' (' . $dsn->description . ')';
        }

        return form::dropdown($name, $connections, $default);
    }

    public function dbmsSelector($name = 'dbType', $default = NULL)
    {
        $availableDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getAvailableDrivers();

        $supportedDrivers = Doctrine_Manager::getInstance()->getCurrentConnection()->getSupportedDrivers();

        $drivers = array_uintersect($availableDrivers, $supportedDrivers, 'strcasecmp');

        foreach($drivers as $driver)
        {
            $driversPDO[$driver] = $driver;
        }

        $dbTypes = $driversPDO;

        return form::dropdown($name, $dbTypes, $default);
    }
}