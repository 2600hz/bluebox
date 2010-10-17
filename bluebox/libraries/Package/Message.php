<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Message
{
    private static $logLevels = array('error', 'alert', 'info', 'success');

    private static $ignore = array();

    /**
     * This function sets a generic message with options
     *
     * @param string the message to display
     * @param array a list of options to apply to the message
     * @return void
     */
    public static function set($text = '', $type = 'error', $key = 'default')
    {
        if (in_array($type, self::$ignore))
        {
            self::log('error', $key .' - ignoring ' .$type .' message: ' .$text);

            return TRUE;
        }

        // if this is an unknown type then invalid
        if (!in_array($type, self::$logLevels))
        {
            self::log('error', 'Invalid message type ' .$type);

            return FALSE;
        }

        // if there is no message then invalid
        if (empty($text))
        {
            self::log('error', 'A message must be supplied.');

            return FALSE;
        }

        // log a success message as debug
        if ($type == 'success')
        {
            self::log('debug', $key .' - ' . $text);
        }
        else
        {
            self::log($type, $key .' - ' . $text);
        }


        // append this message into any existing session bluebox_messages
        if (empty($_SESSION['package_messages']))
        {
            $currentMessages = array();
        }
        else
        {
            $currentMessages = $_SESSION['package_messages'];
        }

        $currentMessages[$type][$key][] = $text;

        $_SESSION['package_messages'] = $currentMessages;

        return TRUE;
    }

    public static function error($text, $type = 'error', $key = 'default')
    {     
        self::set($text, $type, $key);
    }

    public static function warning($text, $type = 'info', $key = 'default')
    {
        self::set($text, $type, $key);
    }

    public static function alert($text, $type = 'alert', $key = 'default')
    {
        self::set($text, $type, $key);
    }

    public static function success($text, $type = 'success', $key = 'default')
    {
        self::set($text, $type, $key);
    }

    public static function log($type, $message)
    {
        kohana::log($type, $message);
    }

    public static function ignoreLogLevels($ignore)
    {
        if (!is_array($ignore))
        {
            $ignore = array($ignore);
        }

        self::$ignore = arr::merge(self::$ignore, $ignore);
    }

    public static function acceptAllLogLevels()
    {
        self::$ignore = array();
    }

    public static function get()
    {
        if (empty($_SESSION['package_messages']))
        {
            return FALSE;
        }

        return $_SESSION['package_messages'];
    }

    public static function clear()
    {
        $_SESSION['package_messages'] = array();
    }
}