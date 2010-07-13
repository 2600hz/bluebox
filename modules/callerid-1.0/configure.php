<?php defined('SYSPATH') or die('No direct access allowed.');

class Callerid_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callerid';
    public static $displayName = 'Caller ID';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Caller ID Plugin';
    public static $description = 'A unified CallerID plugin for supporting caller ID settings in: devices, users, trunks, anyone else who asks for it (via hooks)';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
