<?php defined('SYSPATH') or die('No direct access allowed.');

class CallCenter_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callcenter';
    public static $displayName = 'Call Center';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Call center management tool';
    public static $description = 'A module that allows for managing and adminstrating a call center';
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Call Center';
    public static $navBranch = '/Organization/';
    public static $navURL = 'callcenter/index';
}