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
class SipEncryption_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'sipencryption';
    public static $displayName = 'SIP Encryption';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'SIP Interface Encryption Support';
    public static $description = 'Adds options to support encryption on SIP Interfaces.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}