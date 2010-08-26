<?php defined('SYSPATH') or die('No direct access allowed.');

/* 
This module is, at the moment, a dummy module to get the assets
(jsTree's Javascript) into BlueBox.

It was created by Jort Bloem
*/

class jsTree_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'jstree';
    public static $displayName = 'jsTree Library';
    public static $author = 'jsTree Community';
    public static $vendor = 'http://www.jstree.com/';
    public static $license = 'either the MIT License or the GNU General Public License (GPL) Version 2';
    public static $summary = 'jsTree is a javascript based, cross browser tree component';
    public static $default = false;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array( 'core' => 0.1);

}

