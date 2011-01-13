<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_LocationManager_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if($base instanceof Location)
        {
            foreach($base['User'] as $user)
            {
                foreach($user['Device'] as $device)
                {
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
            $domain = '$${location_' . $device['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $device['device_id']);

            if(isset($device['User']['Location']['registry']['areacode'])) {
                $xml->update('/variables/variable[@name="areacode"]{@value="' . $device['User']['Location']['registry']['areacode'] .'"}');
            }
        }
    }

    public static function delete($base)
    {

    }
}
