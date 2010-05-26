<?php defined('SYSPATH') or die('No direct access allowed.');
class FreePbxManager_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'freepbxmanager';
    public static $displayName = 'Package Manager';
    public static $author = 'K Anderson';
    public static $vendor = 'FreePBX';
    public static $license = 'LGPL';
    public static $summary = 'Management module for the freepbx system.';
    public static $default = TRUE;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $canBeDisabled = FALSE;
    public static $canBeRemoved = FALSE;

    public static $navStructures = array(
        array(
            'navLabel' => 'Maintenance',
            'summary' => 'Perform maintenance on the system',
            'navIcon' => '',
            'navBranch' => '/System/',
            'navURL' => 'freepbxmanager/maintenance'
        ),
        array (
            'navLabel' => 'Package Manager',
            'summary' => 'Install, enable, disable, or uninstall Freepbx packages.',
            'navIcon' => 'assets/img/icons/mainToolsX.png',
            'navBranch' => '/System/',
            'navURL' => 'freepbxmanager/index',
            'navSubmenu' => array(
                'Installed Modules' => '/freepbxmanager/index',
                'Available Packages' => '/freepbxmanager/packages',
                'Settings' => array(
                    'disabled' => 'true',
                    'url' => '/freepbxmanager/settings'
                )
            )
        )
    );


}
