<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_LocationManager_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if($base instanceof Location)
        {
            foreach($base['User'] as $user)
            {
                foreach($user['Device'] as $device)
                {
                    self::setAreacode($device);
                }
            }
        }
        else if($base instanceof Device)
        {
            self::setAreacode($base);
        }
    }

    public static function delete($base)
    {

    }

    public function setAreacode($base)
    {
        if(($base instanceof Device) AND ($location_id = arr::get($base['User'], 'location_id')))
        {
            $domain = '$${location_' .$location_id .'}';

            $xml = Telephony::getDriver()->xml;

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            if (($areacode = arr::get($base['User']['Location'], 'registry', 'areacode')))
            {
                $xml->update('/variables/variable[@name="areacode"]{@value="' .$areacode.'"}');
            }
            else
            {
                $xml->deleteNode('/variables/variable[@name="areacode"]');
            }
        }
    }    
}