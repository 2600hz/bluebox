<?php defined('SYSPATH') or die('No direct access allowed.');
class fax_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'fax';
    public static $displayName = 'Fax';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Fax support';
    public static $description = 'This module configures inbound and outbound faxing support.';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
    	'core' => 0.1
	);
    public static $navLabel = 'Fax';
    public static $navBranch = '/Applications/';
    public static $navURL = 'fax/index';
    public static $navSubmenu = array(
	   	'Create Profile' => array(
			'url' => 'fax/create',
		),
		'Outbound' => array(
			'url' => 'fax/outbound',
		)
	);
	
	public function finalizeInstall()
	{		
		FaxDisposition::register('donothing', 'fax', 'Leave in spool directory');
		FaxDisposition::register('movetodir', 'fax', 'Move to specified directory');
		FaxDisposition::register('emailtoadd', 'fax', 'Email to a specified email');
		FaxDisposition::register('emailtouser', 'fax', 'Email to user');
		
		$tmpdisp = FaxDisposition::getDispositionByName('donothing');

		$infax = new FaxProfile();
		$infax->fxp_name = 'Default';
		$infax->fxp_default = true;
		$infax->fxp_fxd_id = $tmpdisp->fxd_id;
		$infax->fxp_t38_mode = 2;
		$infax->save();
	}
}