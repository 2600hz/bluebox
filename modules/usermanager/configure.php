<?php defined('SYSPATH') or die('No direct access allowed.');
class Usermanager_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'usermanager';
    public static $displayName = 'User Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'This module provides the base functionality to manage users.';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainUsersX.png';
    public static $navBranch = '/Organization/';
    public static $navURL = 'usermanager/index';
    public static $navSubmenu = array(
        'Search Users' => '/usermanager/index',
        'Add User' => '/usermanager/add',
        'Edit User' => array(
            'url' => '/usermanager/edit',
            'disabled' => true
        ) ,
        'Delete User' => array(
            'url' => '/usermanager/delete',
            'disabled' => true
        )
    );
}
