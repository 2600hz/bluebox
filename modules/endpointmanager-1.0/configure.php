<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'endpointmanager';
    public static $displayName = 'Endpoint Manager';
    public static $author = 'Andrew Nagy';
    public static $vendor = 'The Provisioner Project';
    public static $license = 'MPL';
    public static $summary = 'Endpoint provisioning and management tool.';
    public static $description = '<a href="http://projects.colsolgrp.net/projects/show/endpointman">Project Home Page</a>';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Endpoint Manager';
    public static $navBranch = '/Application/';
    public static $navURL = 'endpointmanager/index';
    public static $navSubmenu = array(
        'List' => 'endpointmanager/index',
        'Create' => 'endpointmanager/create',
        'Edit' => array(
            'url' => 'endpointmanager/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => 'endpointmanager/delete',
            'disabled' => true
        )
    );
}