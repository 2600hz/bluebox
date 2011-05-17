<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_agents_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callcenter_agents';
    public static $displayName = 'Call Center Agents';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Configures mod_callcenter agents';
    public static $description = 'This module allows you to configure agents for the mod_callcenter application, which must be compiled and enabled in Freeswitch';
    public static $default = False;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'callcenter_core' => 1.0,
        'feature' => 1.0
     );
    public static $navLabel = 'Agents';
    public static $navBranch = '/Call_Center/';
    public static $navURL = 'callcenter_agents/index';
    public static $navSubmenu = array(
        'List' => 'callcenter_agents/index',
        'Create' => 'callcenter_agents/create',
        'Edit' => array(
            'url' => 'callcenter_agents/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => 'callcenter_agents/delete',
            'disabled' => true
        )
    );

	public function finalizeInstall($identifier)
    {
		CallCenterManager::installFeatures();
		message::set('Default Features Installed', 'alert');
	}
}

?>