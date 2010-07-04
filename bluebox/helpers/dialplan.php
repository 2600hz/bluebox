<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * dialplan.php - dialplan helper class.
 *
 * Provides the ability to hook into various parts of dialplan generation on a global level. This helper provides assistance in registering
 * for dialplan related events that are used when generating telephony events for both config generation realtime and to disk.
 *
 * Created on Aug 19, 2009
 *
 * @author Karl Anderson
 */
class dialplan
{
    public static function register($driver, $hook)
    {
        $driverName = Telephony::getDriverName();
        if ((!$driverName) or ($driverName == 'none')) {
            return true;
        } elseif (!class_exists($driverName))
        {
            Kohana::log('error', 'Unable to register the dialplan driver \'' . Telephony::getDriverName() .'\'');
            return false;
        }

        $driver = Telephony::getDriverName() . '_' . $driver . '_Driver';
        kohana::log('debug', 'EVAL ' . Telephony::getDriverName() . '::getDialplanSections();');
        $sections = eval('return ' . Telephony::getDriverName() . '::getDialplanSections();');

        if (!in_array($hook, $sections)) {
            //Logger::ExceptionByCaller();
            throw new Exception('The hook ' . $hook . ' is not a recognized telephony global hook. (While trying to register callback ' . $driver . ')');
        }

        // Register event as _telephony.action with the callback array as the callback
        Event::add('_telephony.' . $hook, array($driver, $hook));
        Kohana::log('debug', 'Added hook for _telephony.' . $hook . ' to call ' . $driver . '::' . $hook);

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

    // TODO: What is this doing here? This doesn't belong here.
    public static function getTransfer($number_id, $lang = 'XML')
    {
        $numberParts = explode('_', $number_id);
        if (count($numberParts) > 1) {
            $number_id = $numberParts[0];
            $context = 'context_' .$numberParts[1];
        } else {
            $context = self::getContext($number_id);
        }

        $n = Doctrine::getTable('Number')->find($number_id);
        if(!$n)
            return false;

        return sprintf("transfer %s XML %s", $n->number, $context);
    }

    // TODO: What is this doing here? This doesn't belong here.
    public static function getContext($number_id)
    {
        $nv = Doctrine::getTable('NumberContext')->findOneByNumberId($number_id);
        if(!$nv)
            return false;

        return 'context_' . $nv->Context->context_id;
    }
}
