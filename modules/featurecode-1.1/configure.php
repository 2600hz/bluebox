<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureCode_1_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'featurecode';
    public static $displayName = 'Feature Codes';
    public static $author = 'Darren Schreiber';
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

    public function migrate()
    {
        // TODO: Fix this so we migrate properly! Ask Karl
    }
}
