<?php defined('SYSPATH') or die('No direct access allowed.');

class CallIntercept_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $displayName = 'Call Intercept';
    public static $packageName = 'callintercept';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Call Intercept';
    public static $description = 'Allows the user to specify an intercept group for a device';

    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'devicemanager' => 1.0
    );
}
