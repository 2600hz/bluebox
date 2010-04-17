<?php defined('SYSPATH') or die('No direct access allowed.');
class Timezone_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'timezone';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'This module provides the the ability to manage timezones for users and devices.';
    public static $default = false;
    public static $type = FreePbx_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
