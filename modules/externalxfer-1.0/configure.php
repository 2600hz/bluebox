<?php defined('SYSPATH') or die('No direct access allowed.');
class ExternalXfer_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'externalxfer';
    public static $displayName = 'External Transfer Routing Support';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Route calls directly to any external SIP URI or trunk';
    public static $description = 'A plug-in that allows routing a number directly to a SIP uri with no other configuration required or to a trunk that is configured already.';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navLabel = 'External Destination';
    public static $navBranch = '/Routing/';
    public static $navURL = 'externalxfer/index';
    public static $navSubmenu = array(
        'Search External Destination' => 'externalxfer/index',
        'Add External Destination' => 'externalxfer/add',
        'Edit Feature Code' => array(
            'url' => 'externalxfer/edit',
            'disabled' => true
        ) ,
        'Delete External Destination' => array(
            'url' => 'externalxfer/delete',
            'disabled' => true
        )
    );
}
