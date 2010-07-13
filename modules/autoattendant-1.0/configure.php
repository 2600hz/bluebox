<?php defined('SYSPATH') or die('No direct access allowed.');

class AutoAttendant_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'autoattendant';
    public static $displayName = 'Auto Attendant';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Auto Attendant Management and builder';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'mediamanager' => 0.1
    );
    public static $navLabel = 'Auto Attendants';
    public static $navBranch = '/Applications/';
    public static $navURL = 'autoattendant/index';
    public static $navSubmenu = array(
        'Search Auto Attendants' => 'autoattendant/index',
        'Add Auto Attendant' => 'autoattendant/create',
        'Edit Auto Attendant' => array(
            'url' => 'autoattendant/edit',
            'disabled' => true
        ) ,
        'Delete Auto Attendant' => array(
            'url' => 'autoattendant/delete',
            'disabled' => true
        )
    );
}
