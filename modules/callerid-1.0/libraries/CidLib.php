<?php defined('SYSPATH') or die('No direct access allowed.');

class CidLib
{
    public static function initializeDevice()
    {
        extract(Event::$data);

        $plugin = array('callerid' => array(
            'internal_name' => $device['name'],
            'internal_number' => $extension,
            'external_name' => '2600hz blue.box',
            'external_number' => str_pad($extension, 10, "5", STR_PAD_LEFT)
        ));

        $device['plugins'] = arr::merge($device['plugins'], $plugin);
    }
}