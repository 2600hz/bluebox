<?php defined('SYSPATH') or die('No direct access allowed.');

class Core_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'core';
    public static $displayName = 'Bluebox Core';
    public static $author = '2600hz Team';
    public static $vendor = '2600hz Foundation';
    public static $license = 'MPL';
    public static $summary = 'Our free software, your next voip system!';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_CORE;
    public static $required = array();
}