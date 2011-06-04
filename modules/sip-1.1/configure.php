<?php defined('SYSPATH') or die('No direct access allowed.');

class Sip_1_1_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'sip';
    public static $displayName = 'SIP Support';
    public static $author = 'Karl Anderson';
    public static $vendor = 'BitBashing';
    public static $license = 'MPL';
    public static $summary = 'SIP support - adds support for devices, trunks, etc. that use SIP.';
    public static $description = 'This module provides features relevant for connecting SIP-based PBXes, SIP phones and other SIP devices and trunks.';
    public static $default = true;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );
}
