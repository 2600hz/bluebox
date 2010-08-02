<?php defined('SYSPATH') or die('No direct access allowed.');
class Misdn_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'misdn';
    public static $displayName = 'mISDN Driver';
    public static $author = 'Reto Haile';
    public static $vendor = 'Selmoni Ingenieur AG';
    public static $license = 'MPL';
    public static $summary = 'mISDN support - adds support for trunks that use mISDN.';
    public static $description = 'This module provides features relevant for connecting PBXes to ISDN trunks.';
    public static $default = false;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'trunkmanager' => 0.1
    );
}
