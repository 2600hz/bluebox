<?php defined('SYSPATH') or die('No direct access allowed.');

class AccountManager_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'accountmanager';
    public static $displayName = 'Account Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Provides Account Management Tools';
    public static $default = true;
    public static $type = Bluebox_PackageManager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/System/';
    public static $navURL = 'accountmanager/index';
    public static $navSubmenu = array(
        'Search Accounts' => '/accountmanager/index',
        'Add Account' => '/accountmanager/create',
        'Edit Account' => array(
            'url' => '/accountmanager/edit',
            'disabled' => TRUE
        ) ,
        'Delete Account' => array(
            'url' => '/accountmanager/delete',
            'disabled' => TRUE
        )
    );
}
