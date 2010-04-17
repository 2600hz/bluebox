<?php defined('SYSPATH') or die('No direct access allowed.');
class ContextManager_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'contextmanager';
    public static $displayName = 'Context Manager';
    public static $author = 'Darren Schreiber <d@d-man.org>';
    public static $vendor = 'FreePBX';
    public static $license = 'MPL';
    public static $summary = 'Manage contexts for call routing';
    public static $description = 'Allows for management of contexts, allowing you to group together devices, SIP interfaces. These groupings determine what is accessible to inbound and outbound callers.';
    public static $default = true;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Routing/';
    public static $navURL = 'contextmanager/index';
    public static $navSubmenu = array(
        'List Contexts' => '/contextmanager/index',
        'Add Context' => '/contextmanager/add',
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
