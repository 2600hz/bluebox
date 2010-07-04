<?php defined('SYSPATH') or die('No direct access allowed.');
class SimpleRoute_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'simpleroute';
    public static $displayName = 'Simple Route';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Simple Route';
    public static $description = 'A simplified routing mechanism for trunk modules';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'trunkmanager' => 0.1
    );
}
