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
    public static $version = 0.1;
    public static $packageName = 'netlistmanager';
    public static $displayName = 'Network List Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Access Control List Support';
    public static $description = 'Access control lists are lists of domain names, IP addresses and IP address ranges that can be used to identify various network-related activities, such as inbound callers that can bypass authentication, local callers who are on NAT, etc.';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => '0.1'
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
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

    public function createTenant() {
        Bluebox_Installer::disableTelephony();

        $netList = new NetList();
        $netList->name = 'Private/Local Network (auto)';
        $netList->system_list = 'rfc1918.auto';
        $netList->save(TRUE);

        $netList = new NetList();
        $netList->name = 'Private Network (auto)';
        $netList->system_list = 'nat.auto';
        $netList->save(TRUE);

        $netList = new NetList();
        $netList->name = 'Local Network (auto)';
        $netList->system_list = 'localnet.auto';
        $netList->save(TRUE);

        $netList = new NetList();
        $netList->name = 'Loopback Network (auto)';
        $netList->system_list = 'loopback.auto';
        $netList->save(TRUE);


        $netList = new NetList();
        $netList->name = 'Public Internet';
        $netList->allow = TRUE;
        $netList->save(TRUE);

        $netItem = new NetListItem();
        $netItem->NetList = $netList;
        $netItem->record = '0.0.0.0/0';
        $netItem->allow = TRUE;
        $netItem->save(TRUE);

        Bluebox_Installer::restoreTelephony();
    }
}
