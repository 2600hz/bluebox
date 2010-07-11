<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * configure.php - Media Manager Hook
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package Bluebox
 * @subpackage MediaManager
 */
class PowerDns_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'powerdns';
    public static $displayName = 'PowerDNS Management';
    public static $author = 'K Anderson';
    public static $vendor = 'Centrix';
    public static $license = 'MPL';
    public static $summary = 'PowerDNS Management';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Organization/';
    public static $navURL = 'powerdns/index';
    public static $navSubmenu = array(
        'Search Domains' => '/powerdns/index',
        'Add Domain' => '/powerdns/add',
        'Edit Domain' => array(
            'url' => '/powerdns/edit',
            'disabled' => TRUE
        ) ,
        'Delete Domain' => array(
            'url' => '/powerdns/delete',
            'disabled' => TRUE
        )
    );
}
