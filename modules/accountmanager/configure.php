<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * AccountManager_Configure - Configuration metadata / class
 *
 * Gives info about how to install this module
 *
 * @author Darren Schreiber
 * @package FreePBX3
 * @subpackage Account_Manager
 */
class AccountManager_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'accountmanager';
    public static $displayName = 'Account Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Provides Account Management Tools';
    public static $default = FALSE;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Organization/';
    public static $navURL = 'accountmanager/index';
    public static $navSubmenu = array(
        'Search Accounts' => '/accountmanager/index',
        'Add Account' => '/accountmanager/add',
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
