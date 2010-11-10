<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Jon Blanton <jon@2600hz.com>
 * @author Rockwood Cataldo <rocco@2600hz.com>
 * @license MPL
 * @package Xmpp
 */
class Xmpp_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'xmpp';
    public static $author = '<ul><li>Jon Blanton</li><li>Rocco Cataldo</li></ul>';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'XMPP / GTalk integration';
    public static $description = 'This module integrates XMPP support into Bluebox. GTalk (ie Google Voice) is full supported!';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'simpleroute' => 0.1
    );
    public static $navLabel = 'XMPP / GTalk';
    public static $navBranch = '/Applications/';
    public static $navURL = 'xmpp/index';
    public static $navSubmenu = array(
        'List' => 'xmpp/index',
        'Create' => 'xmpp/create',
        'Edit' => array(
            'url' => 'xmpp/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => 'xmpp/delete',
            'disabled' => true
        )
    );
}
