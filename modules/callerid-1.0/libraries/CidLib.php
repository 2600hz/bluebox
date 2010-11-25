<?php defined('SYSPATH') or die('No direct access allowed.');

class CidLib
{
    public static function createExtension()
    {
        Event::$data += array(
            'callerid_internal_name' => Event::$data['owner_name'],
            'callerid_external_name' => Event::$data['owner_name'],
            'callerid_external_number' => str_pad(Event::$data['extension'], 10, "5", STR_PAD_LEFT)
        );

        extract(Event::$data);
        
        $plugin = array('callerid' => array(
            'internal_name' => $callerid_internal_name,
            'internal_number' => $extension,
            'external_name' => $callerid_external_name,
            'external_number' => $callerid_external_number
        ));

        $device['plugins'] = arr::merge($device['plugins'], $plugin);
    }
}