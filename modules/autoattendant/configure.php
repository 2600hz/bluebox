<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * AutoAttendant_Configure - Auto Attendant Configuration metadata / class
 *
 * Gives info about how to setup the Auto Attendant Manager
 *
 * @author Michael Phillips
 * @package Bluebox
 * @subpackage AutoAttendant
 */
class AutoAttendant_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'autoattendant';
    public static $displayName = 'Auto Attendant';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Auto Attendant Management and builder';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'file' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainIVRsX.png';
    public static $navLabel = 'Auto Attendants';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'autoattendant/index';
    public static $navSubmenu = array(
        'Search Auto Attendants' => 'autoattendant/index',
        'Add Auto Attendant' => 'autoattendant/add',
        'Edit Auto Attendant' => array(
            'url' => 'autoattendant/edit',
            'disabled' => true
        ) ,
        'Delete Auto Attendant' => array(
            'url' => 'autoattendant/delete',
            'disabled' => true
        )
    );
}
