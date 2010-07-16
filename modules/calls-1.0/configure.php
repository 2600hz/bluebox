<?php defined('SYSPATH') or die('No direct access allowed.');

class Call_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'calls';
    public static $author = 'Dale Hege';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Call Records and Reporting';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Calls';
    public static $navBranch = '/Reports/';
    public static $navURL = 'calls/index';
    public static $navSubmenu = array(
        'Search Call Records' => '/calls/index',
        'Download Records' => '/calls/download',
        'Import Call Records' => '/calls/import'
    );
}
