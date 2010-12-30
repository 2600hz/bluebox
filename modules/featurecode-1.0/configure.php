<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureCode_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'featurecode';
    public static $displayName = 'Feature Codes';
    public static $author = 'James Aimonetti';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'FreeSWTICH feature code module';
    public static $description = 'Allows configuration of feature codes and stock FreeSWITCH features';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'freeswitch' => 0.1
        ,'core' => 0.1
    );
    public static $navLabel = 'Feature Codes';
    public static $navBranch = '/Applications/';
    public static $navURL = 'featurecode/index';
    public static $navSubmenu = array(
        'Search Feature Codes' => '/featurecode/index'
        ,'Add Feature Codes' => '/featurecode/create'
    );
}
