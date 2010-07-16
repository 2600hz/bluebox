<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * NetList_Configure - Configuration metadata / class
 *
 * Gives info about how to install this module
 *
 * @author Darren Schreiber
 * @package Bluebox
 * @subpackage NetList
 */
class NetList_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'netlistmanager';
    public static $displayName = 'Network List Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Access Control List Support';
    public static $description = 'Access control lists are lists of domain names, IP addresses and IP address ranges that can be used to identify various network-related activities, such as inbound callers that can bypass authentication, local callers who are on NAT, etc.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => '0.1'
    );
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'netlistmanager/index';
    public static $navSubmenu = array(
        'Search Network List' => '/netlistmanager/index',
        'Add Network List' => '/netlistmanager/add',
        'Edit Network List' => array(
            'url' => '/netlistmanager/edit',
            'disabled' => TRUE
        ) ,
        'Delete Network List' => array(
            'url' => '/netlistmanager/delete',
            'disabled' => TRUE
        )
    );
}
