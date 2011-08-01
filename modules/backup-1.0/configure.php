<?php defined('SYSPATH') or die('No direct access allowed.');
class Backup_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'backup';
	public static $displayName = 'Backup';
    public static $author = 'Francis Genet <br /> Peter Defebvre';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Backup of the config.';
    //public static $description = 'Long and detailed description of the modules functionality and usage';
    public static $default = false;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
	public static $navLabel = 'Backup';
    public static $navBranch = '/System/';
    public static $navURL = 'backup/index';
	
}