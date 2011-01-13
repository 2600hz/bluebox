<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_LocationManager_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        Kohana::log("debug", "Setting areacode..." . get_class($base));

        if($base instanceof Location)
        {
            Kohana::log("debug", "this is a location");
            foreach($base['User'] as $user)
            {
                Kohana::log("debug", "this is a user");
                foreach($user['Device'] as $device)
                {
                    Kohana::log("debug", "this is a device");
                    self::setAreacode($device, $xml);
                }
            }
        }
        else if($base instanceof Device)
        {
            self::setAreacode($base, $xml);
        }
    }

    private function setAreacode($device, $xml)
    {
        if($device instanceof Device)
        {
            Kohana::log("debug", "Writing area code to device...");

            $domain = '$${location_' . $device['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $device['device_id']);

            $xml->update('/variables/variable[@name="areacode"]{@value="' . $device['User']['Location']['registry']['areacode'] .'"}');
        }
    }

    public static function delete($base)
    {

    }
}
