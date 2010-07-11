<?php defined('SYSPATH') or die('No direct access allowed.');

class ContextManager_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'contextmanager';
    public static $displayName = 'Context Manager';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'Manage contexts for call routing';
    public static $description = 'Allows for management of contexts, allowing you to group together devices, SIP interfaces. These groupings determine what is accessible to inbound and outbound callers.';
    public static $default = true;
    public static $type = Bluebox_PackageManager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navBranch = '/Routing/';
    public static $navURL = 'contextmanager/index';
    public static $navSubmenu = array(
        'List Contexts' => '/contextmanager/index',
        'Add Context' => '/contextmanager/create',
        'Edit Context' => array(
            'url' => '/contextmanager/edit',
            'disabled' => true
        ) ,
        'Delete Context' => array(
            'url' => '/contextmanager/delete',
            'disabled' => true
        ) ,
    );
}
