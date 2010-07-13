<?php defined('SYSPATH') or die('No direct access allowed.');

class Ringgroup_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'ringgroup';
    public static $displayName = 'Ring Group';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Ring Group Plugin';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Ring Groups';
    public static $navBranch = '/Applications/';
    public static $navURL = 'ringgroup/index';    
    public static $navSubmenu = array(
        'Search Ring Group' => '/ringgroup/index',
        'Add Ring Group' => '/ringgroup/create',
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
