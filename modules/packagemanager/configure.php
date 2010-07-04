<?php defined('SYSPATH') or die('No direct access allowed.');

class PackageManager_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $displayName = 'Package Manager';
    public static $author = 'K Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Management module for the Bluebox package system.';
    public static $default = TRUE;
    public static $type = Bluebox_PackageManager::TYPE_MODULE;
    public static $denyDisable = TRUE;
    public static $denyRemoval = TRUE;

    public static $navLabel = 'Package Manager';
    public static $navSummary = 'Install, enable, disable, or uninstall bluebox packages.';
    public static $navBranch = '/System/';
    public static $navURL = 'packagemanager/index';
}
