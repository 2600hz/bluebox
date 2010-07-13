<?php defined('SYSPATH') or die('No direct access allowed.');

class Sofia_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'sofia';
    public static $displayName = 'SIP Registration Viewer';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Allow end-users to view registrations and much more.';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1
    );
    public static $navBranch = '/Status/';
    public static $navURL = 'sofia/index';    
    public static $navSubmenu = array(
        'Sip Registrations' => '/sofia/index',
        'Details' => array(
            'url' => '/sofia/details',
            'disabled' => true
        )
    );
}
