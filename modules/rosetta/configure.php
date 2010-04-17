<?php defined('SYSPATH') or die('No direct access allowed.');
class Rosetta_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'rosetta';
    public static $displayName = 'Rosetta Automatic Translator';
    public static $author = 'Michael Phillips';
    public static $vendor = 'FreePbx';
    public static $license = 'MPL';
    public static $summary = 'Rosetta automatic language translations.';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_SERVICE;
    public static $required = array(
        'core' => 0.1
    );
}
