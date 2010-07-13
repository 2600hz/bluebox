<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemail_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'voicemail';
    public static $displayName = 'Voicemail Support';
    public static $author = 'K Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Voicemail box support module - allows creation and assignment of voicemail boxes';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Voicemail Boxes';
    public static $navBranch = '/Applications/';
    public static $navURL = 'voicemail/index';
    public static $navSubmenu = array(
        'Voicemail Boxes' => '/voicemail/index',
        'Add Voicemail' => '/voicemail/create',
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
