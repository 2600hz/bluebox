<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalNumber_1_0_1_Configure extends Bluebox_Configure
{
    public static $version = '1.0.0';
    public static $packageName = 'externalnumber';
    public static $displayName = 'External Number Manager';
    public static $author = 'Karl Anderson';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'External Number Management';
    public static $description = '';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => '1.1.1'
    );
    public static $navBranch = '/Routing/';
    public static $navURL = 'externalnumber/index';
    public static $navSubmenu = array(
        'Search Numbers' => '/externalnumber/index',
        'Add a Number' => '/externalnumber/create',
        'Edit a Number' => array(
            'url' => '/externalnumber/edit',
            'disabled' => true
        ) ,
        'Delete Number' => array(
            'url' => '/externalnumber/delete',
            'disabled' => true
        )
    );
}
