<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberManagement_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'numbermanager';
    public static $displayName = 'Number Manager';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Number Management Controller Class';
    public static $description = 'When this module is loaded along with the above listed modules (and possibly others), address fields will automatically appear within relevant modules. In addition, an order of precedence is set for loaded modules as well. This plugin might be overridden by an alternative address module that has better support for international addresses, validation and other features. Make sure you don\'t have conflicts.';
    public static $default = true;
    public static $type = Bluebox_PackageManager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Routing/';
    public static $navURL = 'numbermanager/index';    
    public static $navSubmenu = array(
        'Search Numbers' => '/numbermanager/index',
        'Add a Number' => '/numbermanager/create',
        'Edit a Number' => array(
            'url' => '/numbermanager/edit',
            'disabled' => true
        ) ,
        'Delete Number' => array(
            'url' => '/numbermanager/delete',
            'disabled' => true
        )
    );
}