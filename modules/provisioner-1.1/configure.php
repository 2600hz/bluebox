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
class Provisioner_1_1_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'provisioner';
    public static $author = 'Andrew Nagy';
    public static $vendor = 'The 159';
    public static $license = 'MPL';
    public static $summary = 'The coolest provisioner ever humanly created';
    public static $description = 'Provisioner';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Provisioner';
    public static $navBranch = '/Endpoint/';
    public static $navURL = 'provisioner/index';
}