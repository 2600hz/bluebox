<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_1_1_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'endpointmanager';
    public static $displayName = 'Endpoint Manager';
    public static $author = 'Andrew Nagy';
    public static $vendor = 'The Provisioner Project';
    public static $license = 'MPL';
    public static $summary = 'Endpoint provisioning and management tool.';
    public static $description = '<a href="http://provisioner.net/">Project Home Page</a>';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
}
