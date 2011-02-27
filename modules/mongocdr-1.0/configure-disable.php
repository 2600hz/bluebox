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
class Mongo_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'Mongo CDR';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Michael Phillips';
    public static $license = 'MPL';
    public static $summary = 'MongoDB CDR storage engine ';
    public static $description = 'Store CDRs into MongoDB and get some graphes to go along with it.';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1

    );
    public static $navLabel = 'Mongo CDR';
    public static $navBranch = '/System/';
    public static $navURL = 'mongocdr/index';
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