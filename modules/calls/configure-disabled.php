<?php defined('SYSPATH') or die('No direct access allowed.');
class Calls_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'calls';
    public static $displayName = 'Call Reports';
    public static $author = 'Karl Anderson';
    public static $vendor = 'FreePbx';
    public static $license = 'MPL';
    public static $summary = 'Call Reporting';
    public static $description = '';
    public static $default = false;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainCallsX.png';
    public static $navBranch = '/Reports/';
    public static $navURL = 'calls/index';

    public static function _checkExp() {
        return array('warnings' => 'This module is experimental and not ready for production use!');
    }
}
