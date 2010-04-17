<?php defined('SYSPATH') or die('No direct access allowed.');
class Voicemail_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'voicemail';
    public static $displayName = 'Voicemail Support';
    public static $author = '<ul><li>Michael Phillips</li><li>K Anderson</li></ul>';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Voicemail box support module - allows creation and assignment of voicemail boxes';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Voicemail Boxes';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'voicemail/index';
    public static $navSubmenu = array(
        'Voicemail Boxes' => '/voicemail/index',
        'Add Voicemail' => '/voicemail/add',
        'Edit Voicemail' => array(
            'url' => '/voicemail/edit',
            'disabled' => TRUE
        ) ,
        'Delete Voicemail' => array(
            'url' => '/voicemail/delete',
            'disabled' => TRUE
        )
    );
}
