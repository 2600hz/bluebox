<?php defined('SYSPATH') or die('No direct access allowed.');

class Areacode_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'areacode';
    public static $author = 'Jon Blanton';
    public static $vendor = '2600hz';
    public static $license = 'MPL';
    public static $summary = 'Adds the ability to specify an areacode';
    public static $description = 'By adding the ability to specify an areacode, it enables the ability to 7-digit dail from trunks that require 10-digits';

    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'trunkmanager' => 1.0,
	'devicemanager' => 1.0
    );
}
