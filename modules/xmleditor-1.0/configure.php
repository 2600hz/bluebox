<?php defined('SYSPATH') or die('No direct access allowed.');
class XmlEditor_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'xmleditor';
    public static $displayName = 'XML File Editor';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Direct XML file editor (works on local filesystem only)';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1
    );
    public static $navLabel = 'XML File Editor';
    public static $navBranch = '/System/';
    public static $navURL = 'xmleditor/index';
}
