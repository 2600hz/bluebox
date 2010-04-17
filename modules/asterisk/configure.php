<?php defined('SYSPATH') or die('No direct access allowed.');
class Asterisk_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'asterisk';
    public static $displayName = 'Asterisk Driver';
    public static $author = 'Karl Anderson';
    public static $vendor = 'FreePBX';
    public static $license = 'LGPL';
    public static $summary = 'Driver for Asterisk';
    public static $default = FALSE;
    public static $type = FreePbx_Installer::TYPE_DRIVER;
    public static $required = array(
        'core' => 0.1,
        'not' => array(
            'freeswitch' => '0.1'
        )
    );

    public static function _checkExp() {
        return array('warnings' => 'This module is experimental and not ready for production use!');
    }
}
