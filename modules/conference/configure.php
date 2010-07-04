<?php defined('SYSPATH') or die('No direct access allowed.');

class Conferences_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'conference';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Conference Management';
    public static $default = TRUE;
    public static $type = Bluebox_PackageManager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Conferences';
    public static $navBranch = '/Applications/';
    public static $navURL = 'conference/index';
    public static $navSubmenu = array(
        'Search Conferences' => '/conference/index',
        'Add Conference' => '/conference/create',
        'Edit Conference' => array(
            'url' => '/conference/edit',
            'disabled' => TRUE
        ) ,
        'Delete Conference' => array(
            'url' => '/conference/delete',
            'disabled' => TRUE
        )
    );
}
