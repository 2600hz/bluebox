<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalXferSimple_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'externalxfersimple';
    public static $displayName = 'External Transfer (Simple)';
    public static $author = 'Darren Schreiber & Jort Bloem';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Route calls directly to any external SIP URI or trunk (simple version)';
    public static $description = 'A simple plug-in that allows routing a number directly to a SIP uri with no other configuration required or to a trunk that is configured already.';
    public static $default = false;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
	'externalxfer'=>'1.0',
        'or' => array(
            'sipinterface' => 0.1,
            'trunk' => 0.1
        )
    );
    
    public static $navLabel = 'Speed Dials';
    public static $navBranch = '/Routing/';
    public static $navURL = 'externalxfersimple/index';
    public static $navSubmenu = array(
        'Search External Destination' => 'externalxfersimple/index',
        'Add External Destination' => 'externalxfersimple/create',
        'Edit Feature Code' => array(
            'url' => 'externalxfersimple/edit',
            'disabled' => true
        ) ,
        'Delete External Destination' => array(
            'url' => 'externalxfersimple/delete',
            'disabled' => true
        )
    );
}
