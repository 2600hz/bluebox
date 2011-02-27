<?php defined('SYSPATH') or die('No direct access allowed.');
class Welcomemail_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'welcomemail';
    public static $displayName = 'Welcome Mail';
    public static $author = 'Jort Bloem';
    public static $vendor = 'BTG';
    public static $license = 'MPL';
    public static $summary = 'Sends a friendly welcome email when devices are set up';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array( 
		'devicemanager'=>1.0,
		'core' => 0.1);
    public static $navBranch = '/Organization/';
    public static $navURL = '/welcomemail';
    public static $navSubmenu = array ();

}

