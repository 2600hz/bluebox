<?php defined('SYSPATH') or die('No direct access allowed.');
class Ddndir_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'dbndir';
    public static $displayName = 'Dial By Name Directory';
    public static $author = 'Rob Hutton';
    public static $vendor = 'Dawn Communications';
    public static $license = 'MPL';
    public static $summary = 'Dial By Name Directory';
    public static $description = 'Module and plugins to configure Dial By Name Directories';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
    	'feature' => 1.0
    );
    public static $navLabel = 'Dial-By-Name';
    public static $navBranch = '/Applications/';
    public static $navURL = 'dbndir/index';
    public static $navSubmenu = array(
        'Add Directory' =>  array(
            'url' => 'dbndir/create',
            'disabled' => true
        )
    );

    public function postInstall($identifier)
    {
		$dirobj = new Dbndir();
		$dirobj->dbn_name = 'Default';
		$dirobj->save();
	}
}
?>