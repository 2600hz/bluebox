<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SipEncryption_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if ($base instanceof SipInterface)
        {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' . $base['sipinterface_id']);
            $xml->update('/settings/param[@name="ssl-enable"]{@value="true"}');
            $xml->update('/settings/param[@name="tls-port"]{@value="' .$base['plugins']['sipencryption']['port'] . '"}');
            $xml->update('/settings/param[@name="tls-version"]{@value="' .$base['plugins']['sipencryption']['type'] . '"}');
            $xml->update('/settings/param[@name="ssl-dir"]{@value="' . str_replace('/', '\/', $base['plugins']['sipencryption']['certdir']) . '"}');
        }
    }

    public static function delete($base)
    {
    }
}
