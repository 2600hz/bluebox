<?php defined('SYSPATH') or die('No direct access allowed.');

class SharedPresence_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'sharedpresence';
    public static $displayName = 'Shared Presence';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Shared presence supprt';
    public static $description = 'By default, each SIP Interface has its own presence database. This module allows interfaces to share a presence database.  For instance, if you have an interface for phones on the local network and one that connects across the Internet, this module allows the phones on both interfaces to see each other\'s presence information.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Shared Presence';
    public static $navBranch = '/Status/';
    public static $navURL = 'sharedpresence/index';
    public static $navSubmenu = array(
		'Create' => array(
			'url' => 'sharedpresence/create',
		)
    );
}

?>