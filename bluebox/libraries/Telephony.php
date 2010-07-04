<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Telephony Driver interface for Bluebox
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage Core
 */
class Telephony
{
    public static $filesUpdated = array();

    /**
     * Telephony configuration object. Contains the current configuration information for whatever data is going to be written
     * to disk. This often contains only a partial set of configuration data if only a small portion of data is being adjusted.
     * The driver underneath needs to compensate for the fact that only partial configs may be given at any time.
     *
     * @var Telephony_Configuration
     */
    public static $driver = NULL;

    /**
     * The driver name to use
     * @var string Driver name to use
     */
    private static $driverName;

    public static function getDriverName()
    {
        if ((!self::$driverName) and (Kohana::config('telephony.driver')))
        {
            Telephony::setDriver(Kohana::config('telephony.driver'));
        }
        
        return self::$driverName;
    }

    /**
     * Get a reference to the Telephony driver currently instantiated
     * @return Telephony_Driver
     */
    public static function getDriver()
    {
        if ((!self::$driverName) and (Kohana::config('telephony.driver')))
        {
            Telephony::setDriver(Kohana::config('telephony.driver'));
        }

        return self::$driver;
    }

    /**
     * Set the default driver for future configuration requests.
     * NOTE: This may clear any existing configuration in memory.
     * @param string $driverName
     * @return Telephony_Driver
     */
    public static function setDriver($driverName = NULL)
    {
        if ($driverName)
        {
            self::$driverName = $driverName;
        }

        // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
        // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3*/
        if (class_exists($driverName, TRUE))
        {
            self::$driver = eval('return ' .self::$driverName .'::getInstance();');

            // Return the driver object to the caller
            return self::$driver;
        }
        else
        {
            Kohana::log('debug', 'Telephony -> Telephony driver `' .self::$driverName .'` does not exist, ignoring');

            // This class check and NULL return added by KAnderson
            // specificly because during install this class doesnt exists yet...
            self::$driver = NULL;
            
            return self::$driver;
        }

    }

    public static function set($obj)
    {
        $success = FALSE;

        // Sanity check
        if (!is_object($obj))
        {
            return FALSE;
        }

        // Initialize telephony driver / check if already initialized
        $driver = self::getDriver();

        $driverName = get_class($driver);
        
        // If no driver is set, just return w/ FALSE.
        if (!$driver)
        {
            return FALSE;
        }

        // Support for column aggregation automagically as well as special handling of dialplan/numbers
        if ((get_parent_class($obj) != 'Doctrine_Record') and (get_parent_class($obj) != 'Bluebox_Record')) 
        {
            $objectName = get_parent_class($obj);
        }
        else
        {
            $objectName = get_class($obj);
        }

        $modelDriverName = $driverName .'_' .$objectName .'_Driver';

        // Does the [Doctrine] object we were just passed contain a relevant driver? If so, call it's driver method
        if (class_exists($modelDriverName, TRUE))
        {
            // Get base model to give to the set() routine, for reference
            $base = Bluebox_Record::getBaseTransactionObject();

            $identifier = $obj->identifier();

            $identifier = implode(', ', $identifier);

            // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
            // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3*/
            // TODO: wrap the eval in a try...catch we need this detail logging for the time being but this will need to be addressed or re-thrown */
            try
            {
                Kohana::log('debug', 'Telephony -> Updating information from ' .get_class($obj) .'(' .$objectName .') ' .$identifier .' with OID ' .$obj->getOid() .' on base model ' .get_class($base));
                
                kohana::log('debug', 'EVAL ' . $modelDriverName . '::set($obj, $base);');

                $success = eval('return ' . $modelDriverName . '::set($obj, $base);');
            } 
            catch (Exception $e)
            {
                Kohana::log('error', 'Telephony -> Eval exception: "' . $objectName . '". ' . $e->getMessage());
            }

            if (!empty($obj['plugins']))
            {
                foreach ($obj['plugins'] as $name => $data)
                {
                    $pluginName = ucfirst($name);

                    $pluginDriverName = $driverName .'_' .$pluginName .'_Driver';

                    if (!class_exists($pluginDriverName))
                    {
                        continue;
                    }

                    Kohana::log('debug', 'Telephony -> Updating information from plugin ' .$pluginName .' on ' .$objectName .' ' .$identifier);

                    try
                    {
                        kohana::log('debug', 'EVAL ' . $pluginDriverName . '::set($obj, $base);');

                        $success = eval('return ' . $pluginDriverName . '::set($obj, $base);');
                    }
                    catch (Exception $e)
                    {
                        Kohana::log('error', 'Telephony -> Eval exception: "' . $pluginName . '". ' . $e->getMessage());
                    }
                }
            }
        } 
        else
        {
            Kohana::log('debug', 'Telephony -> No driver for model "' . $objectName . '" for our telephony configuration...');
        }

        Kohana::log('debug', 'Telephony -> Done updating information from model "' . $objectName . '".');

        return $success;
    }

    public static function delete($obj)
    {
        $success = FALSE;

        // Sanity check
        if (!is_object($obj))
        {
            return FALSE;
        }

        // Initialize telephony driver / check if already initialized
        $driver = self::getDriver();

        $driverName = get_class($driver);

        // If no driver is set, just return w/ FALSE.
        if (!$driver)
        {
            return FALSE;
        }

        // Support for column aggregation automagically as well as special handling of dialplan/numbers
        if ((get_parent_class($obj) != 'Doctrine_Record') and (get_parent_class($obj) != 'Bluebox_Record'))
        {
            $objectName = get_parent_class($obj);
        }
        else
        {
            $objectName = get_class($obj);
        }

        $modelDriverName = self::$driverName . '_' . $objectName . '_Driver';

        // Does the [Doctrine] object we were just passed contain a relevant driver? If so, call it's driver method
        if (class_exists($modelDriverName, TRUE))
        {
            // Get base model to give to the set() routine, for reference
            $base = Bluebox_Record::getBaseTransactionObject();

            $identifier = $obj->identifier();

            $identifier = implode(', ', $identifier);
            
            try
            {
                Kohana::log('debug', 'Telephony -> Deleting information from ' .get_class($obj) .'(' .$objectName .') ' .$identifier .' with OID ' .$obj->getOid() .' on base model ' .get_class($base));

                kohana::log('debug', 'EVAL ' . $modelDriverName . '::delete($obj, $base);');

                $success = eval('return ' . $modelDriverName . '::delete($obj, $base);');
            }
            catch (Exception $e)
            {
                Kohana::log('error', 'Telephony -> Eval exception: "' . $objectName . '". ' . $e->getMessage());
            }
            
            if (!empty($obj['plugins']))
            {
                foreach ($obj['plugins'] as $name => $data)
                {
                    $pluginName = ucfirst($name);

                    $pluginDriverName = $driverName .'_' .$pluginName .'_Driver';

                    if (!class_exists($pluginDriverName))
                    {
                        continue;
                    }

                    Kohana::log('debug', 'Telephony -> Deleting information from plugin ' .$pluginName .' on ' .$objectName .' ' .$identifier);

                    try
                    {
                        kohana::log('debug', 'EVAL ' . $pluginDriverName . '::delete($obj, $base);');

                        $success = eval('return ' . $pluginDriverName . '::delete($obj, $base);');
                    }
                    catch (Exception $e)
                    {
                        Kohana::log('error', 'Telephony -> Eval exception: "' . $pluginName . '". ' . $e->getMessage());
                    }
                }
            }

        }
        else
        {
            Kohana::log('debug', 'Telephony -> No driver for model "' . $objectName . '" for our telephony configuration...');
        }

        Kohana::log('debug', 'Telephony -> Done deleting information from model "' . $objectName . '".');

        return $success;
    }

    public static function render()
    {
        $driver = self::getDriver();

        // Render the config and return it
        return $driver->render();
    }

    public static function load($options)
    {
        // Prep/load any config data from disk related to this object. This is optional - the driver does not need to implement this.
        // The purpose of making this available is to not clobber existing configurations on disk

        // Initialize telephony driver / check if already initialized
        $driver = self::getDriver();

        // If no driver is set, just return w/ FALSE.
        if (!$driver)
        {
            return FALSE;
        }
            
        $driver->load($options);
    }

    public static function save($options = NULL)
    {
        // Prep/load any config data from disk related to this object. This is optional - the driver does not need to implement this.
        // The purpose of making this available is to not clobber existing configurations on disk

        // Initialize telephony driver / check if already initialized
        $driver = self::getDriver();

        // If no driver is set, just return w/ FALSE.
        if (!$driver)
        {
            return FALSE;
        }
        
        $driver->save($options);
    }

    public static function commit()
    {
        // Initialize telephony driver / check if already initialized
        $driver = self::getDriver();

        // If no driver is set, just return w/ FALSE.
        if (!$driver)
        {
            return FALSE;
        }

        // Go reload the config in memory on the switch
        $driver->commit();            
    }

    public static function reset()
    {
        $driver = self::getDriver();

        if (!$driver)
        {
            return FALSE;
        }
        
        $driver->reset();
    }
}
