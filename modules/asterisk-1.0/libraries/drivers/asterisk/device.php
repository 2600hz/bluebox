<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * device.php - Asterisk Device driver
 *
 * Allows for a number to terminate at a device directly
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Asterisk_Driver
 */
class Asterisk_Device_Driver extends Asterisk_Base_Driver {
    public static function set($obj)
    {

    }

    public static function delete($obj)
    {
        
    }

    public static function dialplan($obj)
    {
        $doc = Telephony::getDriver()->doc;
        
        if ($obj->Device->class_type == 'SipDevice') {
            Doctrine::initializeModels('SipDevice');
            $doc->add('Dial(SIP/' . $obj->Device->Sip->username .')');
        }
    }
}
