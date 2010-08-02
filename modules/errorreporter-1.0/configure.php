<?php defined('SYSPATH') or die('No direct access allowed.');

class Error_Reporter_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'errorreporter';
    public static $displayName = 'Error Reporter';
    public static $author = 'K Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Service user to report and recieve help with errors';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Report Bug';
    public static $navBranch = '/System/';
    public static $navURL = 'errorreporter/inform';
}