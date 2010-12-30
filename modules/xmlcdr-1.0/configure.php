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
class Xmlcdr_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'XML CDR';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Michael Phillips';
    public static $license = 'MPL';
    public static $summary = 'xmlcdr ';
    public static $description = 'xmlcdr';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1

    );
    public static $navLabel = 'CDR';
    public static $navBranch = '/System/';
    public static $navURL = 'xmlcdr/index';
    public static $navSubmenu = array(
        'CDRs' => 'xmlcdr/index'

    );
}