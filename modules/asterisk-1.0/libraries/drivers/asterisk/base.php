<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Base Asterisk Module Driver. Extend this class when creating modules that spit out configuration information.
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Asterisk_Driver
 */
interface Asterisk_Base_Driver_Interface {
    public static function set($obj);
        // Must be defined by implementor!

    public static function delete($obj);
        // Must be defined by implementor!

}

abstract class Asterisk_Base_Driver implements Asterisk_Base_Driver_Interface {
    public static function dialplan($obj)
    {
        return FALSE;   // Not supported if not overridden
    }
}
