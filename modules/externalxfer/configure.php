<?php defined('SYSPATH') or die('No direct access allowed.');
class ExternalXfer_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'externalxfer';
    public static $displayName = 'External Transfer Routing Support';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Route calls directly to any external SIP URI or trunk';
    public static $description = 'A plug-in that allows routing a number directly to a SIP uri with no other configuration required or to a trunk that is configured already.';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
