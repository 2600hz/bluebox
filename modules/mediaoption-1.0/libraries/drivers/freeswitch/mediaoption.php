<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_MediaOption_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if (empty($base['plugins']['mediaoption']))
        {
            return;
        }

        $mediaoption = $base['plugins']['mediaoption'];

        if ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            if (empty($mediaoption['bypass_media']))
            {
                $xml->update('/variables/variable[@name="bypass_media"]{@value="no"}');
            }
            else
            {
                $xml->update('/variables/variable[@name="bypass_media"]{@value="yes"}');
            }
        }
    }

    public static function delete($base)
    {
        if ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            $xml = FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->deleteNode('/variables/variable[@name="bypass_media"]');
        }
    }
}
