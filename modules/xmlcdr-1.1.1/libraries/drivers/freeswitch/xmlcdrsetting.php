<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_XmlcdrSetting_Driver extends FreeSwitch_Base_Driver {

    public static function set($base)
    {
        $settings = $base['registry'];

        $xml = Telephony::getDriver()->xml;

        FreeSwitch::setSection('xmlcdr');

        foreach( $settings as $name => $value ) {
            $xml->update('/settings/param[@name="' . $name . '"]{@value="' . str_replace('/', '\/', $value) . '"}');
        }

  
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        FreeSwitch::setSection('xmlcdr');

        //not sure what to do here

//        $xml->deleteNode();
    }
}
