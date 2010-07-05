<?php defined('SYSPATH') or die('No direct access allowed.');
class BlueboxManager_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'blueboxmanager';
    public static $displayName = 'Package Manager';
    public static $author = 'K Anderson';
    public static $vendor = 'Bluebox';
    public static $license = 'LGPL';
    public static $summary = 'Management module for the bluebox system.';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
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
            'navURL' => 'blueboxmanager/maintenance'
        ),
        array (
            'navLabel' => 'Package Manager',
            'summary' => 'Install, enable, disable, or uninstall Bluebox packages.',
            'navIcon' => 'assets/img/icons/mainToolsX.png',
            'navBranch' => '/System/',
            'navURL' => 'blueboxmanager/index',
            'navSubmenu' => array(
                'Installed Modules' => '/blueboxmanager/index',
                'Available Packages' => '/blueboxmanager/packages',
                'Settings' => array(
                    'disabled' => 'true',
                    'url' => '/blueboxmanager/settings'
                )
            )
        )
    );


}
