<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_queues_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callcenter_queues';
    public static $displayName = 'Call Center Queues';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Configures mod_callcenter call queues';
    public static $description = 'This module allows you to configure call queues for the mod_callcenter application, which must be compiled and enabled in Freeswitch';
    public static $default = False;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'callcenter_core' => 1.0
     );
    public static $navLabel = 'Queues';
    public static $navBranch = '/Call_Center/';
    public static $navURL = 'callcenter_queues/index';
    public static $navSubmenu = array(
        'List' => 'callcenter_queues/index',
        'Create' => 'callcenter_queues/create',
        'Edit' => array(
            'url' => 'callcenter_queues/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => 'callcenter_queues/delete',
            'disabled' => true
        )
    );
}

?>