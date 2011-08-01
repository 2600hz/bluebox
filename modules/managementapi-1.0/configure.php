<?php defined('SYSPATH') or die('No direct access allowed.');

class ManagementAPI_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'managementapi';
    public static $displayName = 'Management APIs';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Management APIs - allows for adding extensions and trunks as well as a few other neat things.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_SERVICE;
    public static $required = array(
        'core' => 0.1
    );
}
