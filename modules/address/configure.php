<?php defined('SYSPATH') or die('No direct access allowed.');
class Address_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'address';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Address Management Plugin';
    public static $description = 'When this module is loaded along with the above listed modules (and possibly others), address fields will automatically appear within relevant modules. In addition, an order of precedence is set for loaded modules as well. This plugin might be overridden by an alternative address module that has better support for international addresses, validation and other features. Make sure you don\'t have conflicts.';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
