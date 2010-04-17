<?php defined('SYSPATH') or die('No direct access allowed.');
class FeatureCode_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'featurecode';
    public static $displayName = 'Feature Codes';
    public static $author = 'Michael Phillips';
    public static $vendor = 'FreePbx';
    public static $license = 'MPL';
    public static $summary = 'FreeSWTICH feature code module';
    public static $description = 'Allows configuration of feature codes';
    public static $default = FALSE;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'freeswitch' => 0.1,
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'featurecode/index';

    public static function _checkExp() {
        return array('warnings' => 'This module is experimental and not ready for production use!');
    }
}
