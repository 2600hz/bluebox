<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 *
 * ============================================================================
 * NOTE: You must rename this file 'configure.php' or the package manager will
 *      ignore your plugin!  Any change you make here after install will
 *      require you to repair you plugin via the package manager.
 * ============================================================================
 */
class MyPlugin_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'myplugin';
    public static $author = 'Your Name';
    public static $vendor = 'Your Vendor';
    public static $license = 'MPL';
    public static $summary = 'Short summary of plugin functionality';
    public static $description = 'Long and detailed description of the plugins functionality and usage';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}