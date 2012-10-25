<?php defined('SYSPATH') or die('No direct access allowed.');

class SipLib
{
    public static function createExtension()
    {
        Event::$data += array(
            'sip_username' => html::token(Event::$data['owner_name']),
            'sip_password' => inflector::generatePassword()
        );

        extract(Event::$data);

        $plugin = array('sip' => array(
            'username' => $sip_username,
            'password' => $sip_password
        ));

        $device['plugins'] = arr::merge($device['plugins'], $plugin);
    }
}