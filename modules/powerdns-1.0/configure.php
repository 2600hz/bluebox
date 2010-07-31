<?php defined('SYSPATH') or die('No direct access allowed.');

class PowerDns_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'powerdns';
    public static $displayName = 'PowerDNS Management';
    public static $author = 'K Anderson';
    public static $vendor = 'Centrix';
    public static $license = 'MPL';
    public static $summary = 'PowerDNS Management';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Organization/';
    public static $navURL = 'powerdns/index';
    public static $navSubmenu = array(
        'Search Domains' => '/powerdns/index',
        'Add Domain' => '/powerdns/create',
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
