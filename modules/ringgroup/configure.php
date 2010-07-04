<?php defined('SYSPATH') or die('No direct access allowed.');
class Ringgroup_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'ringgroup';
    public static $displayName = 'Ring Group';
    public static $author = 'K Anderson';
    public static $vendor = 'Bluebox';
    public static $license = 'LGPL';
    public static $summary = 'Ring Group Plugin';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navLabel = 'Ring Groups';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'ringgroup/index';    
    public static $navSubmenu = array(
        'Search Ring Group' => '/ringgroup/index',
        'Add Ring Group' => '/ringgroup/add',
        'Edit Ring Group' => array(
            'url' => '/ringgroup/edit',
            'disabled' => true
        ) ,
        'Delete Ring Group' => array(
            'url' => '/ringgroup/delete',
            'disabled' => true
        )
    );
}
