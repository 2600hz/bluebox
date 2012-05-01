<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberCleanup_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'numbercleanup';
    public static $displayName = 'Number Cleanup';
    public static $author = 'Jort Bloem';
    public static $vendor = 'Business Technology Group';
    public static $license = 'MPL';
    public static $summary = 'Allows modification of numbers.';
    public static $description = '<a href="http://www.btg.co.nz/">Business Technology Group</a>';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );

}
