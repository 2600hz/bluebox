<?php defined('SYSPATH') or die('No direct access allowed.');

class VoicemailViewer_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'voicemailviewer';
    public static $displayName = 'Voicemail Viewer';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Voicebus';
    public static $license = 'MPL';
    public static $summary = 'Allows you to view, listen, delete save voicemail messages';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Applications/';
    public static $navURL = 'voicemailviewer/index';
    public static $navSubmenu = array(
        'Search Domains' => '/voicemailviewer/index',
        'Add Domain' => '/voicemailviewer/add',
        'Edit Domain' => array(
            'url' => '/voicemailviewer/edit',
            'disabled' => TRUE
        ) ,
        'Delete Domain' => array(
            'url' => '/voicemailviewer/delete',
            'disabled' => TRUE
        )
    );
}
