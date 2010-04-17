<?php defined('SYSPATH') or die('No direct access allowed.');
class MediaOption_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'mediaoption';
    public static $displayName = 'Media Options';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Media Options support - allows for specifying how media is handled for devices, trunks, etc.';
    public static $description = 'This module allows for advanced codec and media handling settings for trunks, devices and other media-handling items connected to a PBX or phone system';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
