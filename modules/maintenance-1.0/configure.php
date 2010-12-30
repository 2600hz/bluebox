<?php defined('SYSPATH') or die('No direct access allowed.');

class Maintenance_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'maintenance';
    public static $displayName = 'Maintenance';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BlueBox';
    public static $license = 'MPL';
    public static $summary = 'Service used to trigger maintenance events';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_SERVICE;
    public static $required = array(
        'core' => 0.1
    );
}