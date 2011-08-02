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
class Intercom_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $displayName = 'Intercom';
    public static $packageName = 'intercom';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Plugin for feature module to provide intercom feature';
    public static $description = 'This module plugs in to the feature module to provide the Intercom feature';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'feature' => 1.0
    );

	public function finalizeInstall()
    {
		try {
			Feature::reregister(
				'intercom',
				'intercom',
				'Intercom',
				'Two-way intercom feature, dial feature number and extension',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}
   	}
    
}
?>