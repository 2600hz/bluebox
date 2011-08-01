<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Jon Blanton <jon@2600hz.com>
 * @author Jean-Roch Maitre <jr@2600hz.com>
 * @license MPL
 * @package Active Feature Code
 *
 */
class ActiveFeatureCode_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'activefeaturecode';
    public static $displayName = 'Active Feature Codes';
    public static $author = 'Jon Blanton<br/>Jean-Roch Maitre';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Adds the ability to use on-call feature codes';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
