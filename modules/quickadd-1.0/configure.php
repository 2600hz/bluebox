<?php defined('SYSPATH') or die('No direct access allowed.');

class QuickAdd_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'quickadd';
    public static $displayName = 'Quick Add';
    public static $author = 'Karl Anderson';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Provides a method to create a user, device, number and voicemail at once';
    public static $description = '';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Organization/';
    public static $navURL = '/quickadd/index';
    public static $navSubmenu = array(
        'List' => '/quickadd/index',
        'Add' => '/quickadd/create',
        'Edit' => array(
            'url' => '/quickadd/edit',
            'disabled' => TRUE
        ) ,
        'Delete' => array(
            'url' => '/quickadd/delete',
            'disabled' => TRUE
        )
    );
}