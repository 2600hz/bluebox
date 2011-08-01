<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_core_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callcenter_core';
    public static $displayName = 'Call Center Core';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Core settings for mod_callcenter';
    public static $description = 'This module allows configuration of the core mod_callcenter settings';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'callmanager' => 1.0,
        'feature' => 1.0
     );
    public static $navLabel = 'Settings';
    public static $navBranch = '/Call_Center/';
    public static $navURL = 'callcenter_core/edit/1';

	public function postInstall($identifier)
    {
		$ccs = new callcenter_settings();
		$ccs->id = 1;
		$ccs->save();
	}
}

?>