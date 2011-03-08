<?php defined('SYSPATH') or die('No direct access allowed.');

class Redbox_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'Redbox';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Adds the abiltiy to configure a redbox';
    public static $description = 'Redbox FTW';

    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'devicemanager' => 1.0
    );

    public static $navLabel = 'Redbox';
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'redbox/index';
    public static $navSubmenu = array(
        'Redboxes' => 'redbox/index',
        'Add Redbox' => 'redbox/create',
        'Edit Redbox' => array(
            'url' => 'redbox/edit',
            'disabled' => true
        ) ,
        'Delete Redbox' => array(
            'url' => 'redbox/delete',
            'disabled' => true
        )
    );
}