<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * PermissionManager_Configure - Configuration metadata / class
 *
 * Gives info about how to install this module
 *
 * @author K Anderson
 * @package Bluebox
 * @subpackage PermissionManager_Manager
 */
class PermissionManager_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'permission';
    public static $displayName = 'Permission Manager';
    public static $author = 'K Anderson';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Provides Permission Management';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Permissions';
    public static $navBranch = '/Organization/';
    public static $navURL = 'permission/index';
    public static $navSubmenu = array(
        'Manage Persissions' => 'permission/index',
    );
}
