<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Timezone_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if ($base instanceof Device)
        {
            if (empty($base['plugins']['timezone']))
            {
                return;
            }

            $timezone = $base['plugins']['timezone'];

            $domain = '$${location_' .$base['User']['location_id'] .'}';

            $xml = FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->update('/variables/variable[@name="timezone"]{@value="' . str_replace('/', '\/', $timezone['timezone']) . '"}');
        }
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        if ($base instanceof Device)
        {
            if (empty($base['plugins']['timezone']))
            {
                return;
            }

            $timezone = $base['plugins']['timezone'];

            $domain = '$${location_' .$base['User']['location_id'] .'}';

            $xml = FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->deleteNode('/variables/variable[@name="timezone"]');
        }
    }
}