<?php defined('SYSPATH') or die('No direct access allowed.');
class Trunkmanager_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'trunkmanager';
    public static $displayName = 'Trunk Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Trunk Management Controller Class';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'trunkmanager/index';
    public static $navSubmenu = array(
        'Search Trunks' => '/trunkmanager/index',
        'Add Trunk' => '/trunkmanager/add',
        'Edit Trunk' => array(
            'url' => '/trunkmanager/edit',
            'disabled' => 'true'
        ) ,
        'Delete Trunk' => array(
            'url' => '/trunkmanager/delete',
            'disabled' => true
        )
    );
}
