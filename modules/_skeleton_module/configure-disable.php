<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 *
 * ============================================================================
 * NOTE: You must rename this file 'configure.php' or the package manager will
 *      ignore your module!  Any change you make here after install will
 *      require you to repair you module via the package manager.
 * ============================================================================
 */
class MyModule_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'mymodule';
    public static $author = 'Your Name';
    public static $vendor = 'Your Vendor';
    public static $license = 'MPL';
    public static $summary = 'Short summary of this modules functionality';
    public static $description = 'Long and detailed description of the modules functionality and usage';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'SomeOtherModel' => 1.2
    );
    public static $navLabel = 'My Module';
    public static $navBranch = '/System/';
    public static $navURL = 'mymodule/index';
    public static $navSubmenu = array(
        'List' => 'mymodule/index',
        'Create' => 'mymodule/create',
        'Edit' => array(
            'url' => 'mymodule/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => 'mymodule/delete',
            'disabled' => true
        )
    );
}