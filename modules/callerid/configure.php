<?php defined('SYSPATH') or die('No direct access allowed.');
class Callerid_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'callerid';
    public static $displayName = 'Caller ID';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Caller ID Plugin';
    public static $description = 'A unified CallerID plugin for supporting caller ID settings in: devices, users, trunks, anyone else who asks for it (via hooks)';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
