<?php defined('SYSPATH') or die('No direct access allowed.');

class LocationManager_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'locationmanager';
    public static $displayName = 'Location Manager';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Provides Location Management Tools';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Organization/';
    public static $navURL = 'locationmanager/index';
    public static $navSubmenu = array(
        'Search Locations' => '/locationmanager/index',
        'Add Location' => '/locationmanager/create',
        'Edit Location' => array(
            'url' => '/locationmanager/edit',
            'disabled' => TRUE
        ) ,
        'Delete Location' => array(
            'url' => '/locationmanager/delete',
            'disabled' => TRUE
        )
    );
}
