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
class CallRecord_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callrecord';
    public static $displayName = 'Call Record';
    public static $author = 'Darren Schreiber';
    public static $vendor = '2600hz Project';
    public static $license = 'MPL';
    public static $summary = 'Call Recording Support';
    public static $description = 'Enables call-recording on a per-device and an overall system level';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}