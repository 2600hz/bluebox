<?php defined('SYSPATH') or die('No direct access allowed.');
class Timezone_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'timezone';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'This module provides the the ability to manage timezones for users and devices.';
    public static $default = false;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
