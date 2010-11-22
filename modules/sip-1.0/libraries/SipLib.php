<?php defined('SYSPATH') or die('No direct access allowed.');

class SipLib
{
    public static function initializeDevice()
    {
        extract(Event::$data);

        $plugin = array('sip' => array(
            'username' => html::token($device['name']),
            'password' => inflector::generatePassword()
        ));

        $device['plugins'] = arr::merge($device['plugins'], $plugin);
    }
}