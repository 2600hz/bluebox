<?php defined('SYSPATH') or die('No direct access allowed.');

class callmanager_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callmanager';
    public static $displayName = 'Call Manager';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Manage calls';
    public static $description = 'Allows you to manage active calls or initiate new calls through the web interface.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'esl' => 1.1,
        'callrecord' => 1.0
    );
    public static $navLabel = 'Call Manager';
    public static $navBranch = '/Status/';
    public static $navURL = 'callmanager/index';
    public static $navSubmenu = array(
        'List' => 'callmanager/index'
    );
    
    public function finalizeInstall()
    {
    	callManager::installDefaultFunctions();
		message::set('Default Functions Installed', 'alert');
    }
}