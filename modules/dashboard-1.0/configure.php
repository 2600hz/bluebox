<?php defined('SYSPATH') or die('No direct access allowed.');

class Dashboard_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'dashboard';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Provides content for the welcome page';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
