<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Areacode_Driver
{
    public static function set($base)
    {
        if(($base instanceof Device) AND ($location_id = arr::get($base['User'], 'location_id')))
        {
            $domain = '$${location_' .$location_id .'}';

            $xml = Telephony::getDriver()->xml;

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            if (($areacode = arr::get($base, 'registry', 'areacode')))
            {
                $xml->update('/variables/variable[@name="areacode"]{@value="' .$areacode.'"}');
            }
            else
            {
                $xml->deleteNode('/variables/variable[@name="areacode"]');
            }
	}
    }

    public static function delete($base)
    {
        
    }
}
