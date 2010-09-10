<?php defined('SYSPATH') or die('No direct access allowed.');

class Sessionrecord_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'sessionrecord';
    public static $displayName = 'Session Recording';
    public static $author = 'Dale Hege';
    public static $vendor = '2600hz Team';
    public static $license = 'MPL';
    public static $summary = 'Session Recording Plugin';
    public static $description = 'Currently enables session recording for all calls';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
