<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * LocationManager_Configure - Configuration metadata / class
 *
 * Gives info about how to install this module
 *
 * @author Darren Schreiber
 * @package FreePBX3
 * @subpackage Location_Manager
 */
class LocationManager_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'locationmanager';
    public static $displayName = 'Location Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Provides Location Management Tools';
    public static $default = TRUE;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Organization/';
    public static $navURL = 'locationmanager/index';    
    public static $navSubmenu = array(
        'Search Locations' => '/locationmanager/index',
        'Add Location' => '/locationmanager/add',
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
