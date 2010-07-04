<?php defined('SYSPATH') or die('No direct access allowed.');

class dialplan
{
    protected static $dialplanSections = array();

    public static function register($driver, $hook)
    {
        $driverName = Telephony::getDriverName();

        if ((!$driverName) or ($driverName == 'none'))
        {
            return true;
        }
        elseif (!class_exists($driverName))
        {
            Kohana::log('error', 'Telephony -> Unable to register the dialplan driver \'' .$driverName .'\'');
            
            return false;
        }

        $hookClass = $driverName .'_' .$driver .'_Driver';

        if (!class_exists($hookClass))
        {
            Kohana::log('error', 'Telephony -> Unable to register the dialplan hook \'' .$driver .'\'(' .$hookClass .')');

            return false;
        }

        if (empty(self::$dialplanSections))
        {
            kohana::log('debug', 'Telephony -> EVAL ' .$driverName .'::getDialplanSections();');

            $sections = eval('return ' .$driverName .'::getDialplanSections();');

            if (is_array($sections))
            {
                self::$dialplanSections = $sections;
            }
        }

        if (!in_array($hook, self::$dialplanSections))
        {
            //Logger::ExceptionByCaller();
            throw new Exception('The hook ' .$hook .' is not a recognized telephony global hook. (While trying to register callback ' .$driver . ')');
        }

        // Register event as _telephony.action with the callback array as the callback
        Event::add('_telephony.' .$hook, array($hookClass, $hook));

        Kohana::log('debug', 'Telephony -> Added hook for _telephony.' .$hook .' to call ' .$hookClass .'::' .$hook);

        return TRUE;
    }

    public static function preNumber($obj)
    {
        $driver = Telephony::getDriver();

        // Things to setup before actually connecting to the final destination, such as timeouts/etc.
        $driver->preNumber($obj);
    }

    public static function postNumber($obj)
    {
        $driver = Telephony::getDriver();

        // Failback - when the bridge/route/conference/etc. doesn't work out, where do we go? (If anywhere)
        $driver->postNumber($obj);
    }

    /**
     * This function should be called when you want to start/initialize a context in a dialplan
     * @param string $context Name of Context
     */
    public static function start($context = NULL)
    {
        $driver = Telephony::getDriver();

        // 1. Network setup is first
        $driver->network($context);

        // 2. Now, let's condition any variables or settings
        $driver->conditioning($context);

        // 3. Make any pre-routing decisions (blacklist, virtual numbers, etc.)
        $driver->preRoute($context);

        // 4. Make any post-routing decisions (after-hours, etc.)
        $driver->postRoute($context);

        // 5. Do any pre-answer work (set ring type, moh, etc.)
        $driver->preAnswer($context);

        // 6. Do any post-answer tasks, like forced greetings ("your call may be monitored"), bong tones, etc.
        $driver->postAnswer($context);
    }

    /**
     * This function should be called when you want to end a context in a dialplan
     * @param string $context Context to end
     */
    public static function end($context = FALSE)
    {
        $driver = Telephony::getDriver();

        // 7. Any final things to do? Play a goodbye message? A survey? Write a special log? etc.
        $driver->postExecute($context);
    }
}
