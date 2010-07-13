<?php defined('SYSPATH') or die('No direct access allowed.');

class DeviceManager_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'devicemanager';
    public static $displayName = 'Device Manager';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Provides Device Management';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Devices';
    public static $navBranch = '/Applications/';
    public static $navURL = 'devicemanager/index';
    public static $navSubmenu = array(
        'Search Devices' => 'devicemanager/index',
        'Add Device' => 'devicemanager/create',
        'Edit Device' => array(
            'url' => 'devicemanager/edit',
            'disabled' => true
        ) ,
        'Delete Device' => array(
            'url' => 'devicemanager/delete',
            'disabled' => true
        )
    );
}
