<?php defined('SYSPATH') or die('No direct access allowed.');
class FeatureCode_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'featurecode';
    public static $displayName = 'Feature Codes';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'FreeSWTICH feature code module';
    public static $description = 'Allows configuration of feature codes and stock FreeSWITCH features';
    public static $default = FALSE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'freeswitch' => 0.1,
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navLabel = 'Feature Codes';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'featurecode/index';

    public static $navSubmenu = array(
        'Search Feature Codes' => 'featurecode/index',
        'Add Feature Code' => 'featurecode/add',
        'Edit Feature Code' => array(
            'url' => 'featurecode/edit',
            'disabled' => true
        ) ,
        'Delete Feature Code' => array(
            'url' => 'featurecode/delete',
            'disabled' => true
        )
    );
}
