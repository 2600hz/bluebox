<?php defined('SYSPATH') or die('No direct access allowed.');
class Dash_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'dash';
    public static $displayName = 'Dash';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Dash quick actions.';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_SERVICE;
    public static $required = array(
        'core' => 0.1
    );
}
