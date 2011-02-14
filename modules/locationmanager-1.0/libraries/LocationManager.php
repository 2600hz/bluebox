<?php defined('SYSPATH') or die('No direct access allowed.');

class LocationManager
{
    public static function createExtension()
    {
        extract(Event::$data);

        $plugin = array('locationmanager' => array());

        $device['plugins'] = arr::merge($device['plugins'], $plugin);
    }
}