<?php defined('SYSPATH') or die('No direct access allowed.');
class valetparking_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'valetparking';
    public static $displayName = 'Valet Parking';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Features';
    public static $description = 'This module configures the valet parking dialplan functionn.';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
	);
    public static $navLabel = 'ValetParking';
    public static $navBranch = '/Applications/';
    public static $navURL = 'valetparking/index';
    public static $navSubmenu = array(
		'Create' => array(
			'url' => 'valetparking/create',
		)
    );
}