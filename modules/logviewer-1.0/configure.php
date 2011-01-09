<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 *
 * ============================================================================
 * NOTE: You must rename this file 'configure.php' or the package manager will
 *      ignore your module!  Any change you make here after install will
 *      require you to repair you module via the package manager.
 * ============================================================================
 */
class LogViewer_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'Log Viewer';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Add this later...';
    public static $description = 'Along with this...';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
    );
    public static $navLabel = 'Log Viewer';
    public static $navBranch = '/System/';
    public static $navURL = 'logviewer/index';
}