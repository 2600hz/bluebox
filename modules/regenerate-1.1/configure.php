<?php defined('SYSPATH') or die('No direct access allowed.');

class Regenerate_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'regenerate';
    public static $displayName = 'Regenerate Config';
    public static $author = 'Dale Hege';
    public static $vendor = '2600hz Team';
    public static $license = 'MPL';
    public static $summary = 'Config Regeneration Module';
    public static $description = 'Regenerate all configs based on database';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Regenerate Configs';
    public static $navURL = 'regenerate/index';
    public static $navBranch = '/System/';


}
