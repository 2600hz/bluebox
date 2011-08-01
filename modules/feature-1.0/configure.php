<?php defined('SYSPATH') or die('No direct access allowed.');

class features_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'feature';
    public static $displayName = 'Features';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Features';
    public static $description = 'This module configures the features that are available in feature codes.';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
	);
    public static $navLabel = 'Features';
    public static $navBranch = '/Applications/';
    public static $navURL = 'feature/index';
    public static $navSubmenu = array(
		'Create' => array(
			'url' => 'feature/create',
		)
    );

	public function finalizeInstall()
    {
		FeatureManager::installDefaultFeatures();
		message::set('Default Features Installed', 'alert');
	}
}

?>