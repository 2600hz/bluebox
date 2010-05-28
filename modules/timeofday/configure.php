<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDay_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'timeofday';
    public static $displayName = 'Time Based Routes';
    public static $author = 'K Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Time Based Routes';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'timeofday/index';
    public static $navSubmenu = array(
        'Search Time Based Routes' => 'timeofday/index',
        'Add Time Based Route' => 'timeofday/add',
        'Edit Time Based Route' => array(
            'url' => 'timeofday/edit',
            'disabled' => true
        ) ,
        'Delete Time Based Route' => array(
            'url' => 'timeofday/delete',
            'disabled' => true
        )
    );
}
