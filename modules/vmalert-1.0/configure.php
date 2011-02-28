<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Jon Blanton <jon@2600hz.com>
 * @license MPL
 * @package VMAlert-1.0
 */
class VMAlert_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'vmalert';
    public static $displayName = 'Vociemail Alert';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Enables option to be notified when you are left Voicemail';
    public static $description = '(see summary for now...)';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'voicemail' => 0.1
    );
}