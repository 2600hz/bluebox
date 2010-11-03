<?php defined('SYSPATH') or die('No direct access allowed.');

class MediaFile_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'mediafile';
    public static $displayName = 'Media File';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Media File allows users and admins to upload media to the system';
    public static $description = 'This module handles exposing media files to the system and media resources to other modules such as Music On Hold, AutoAttendant, ect.';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Media/';
    public static $navURL = '/mediafile/index';
    public static $navSubmenu = array(
        'List Media' => '/mediafile/index',
        'Add Media' => '/mediafile/create',
        'Edit Media' => array(
            'url' => '/mediafile/edit',
            'disabled' => TRUE
        ) ,
        'Delete Media' => array(
            'url' => '/mediafile/delete',
            'disabled' => TRUE
        )
    );
}