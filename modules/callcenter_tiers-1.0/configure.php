<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_tiers_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callcenter_tiers';
    public static $displayName = 'Call Center Tiers';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Configures Call Center tiers';
    public static $description = 'This module allows you to configure agent tiers for a call center';
    public static $default = False;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'callcenter_core' => 1.0
     );
    public static $navLabel = 'Tiers';
    public static $navBranch = '/Call_Center/';
    public static $navURL = 'callcenter_tiers/index';
    public static $navSubmenu = array(
        'List' => 'callcenter_tiers/index',
        'Create' => 'callcenter_tiers/create',
        'Edit' => array(
            'url' => 'callcenter_tiers/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => 'callcenter_tiers/delete',
            'disabled' => true
        )
    );
}

?>