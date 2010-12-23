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
    public static $description = '<a href="http://projects.colsolgrp.net/projects/show/endpointman">Project Home Page</a>';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navLabel = 'Endpoint Manager';
    public static $navBranch = '/Applications/';
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

    public function postInstall() {
        $brand = new EndpointBrand();
        $brand['name'] = 'Aastra';
        $brand['directory'] = '';
        $brand['cfg_ver'] = '';
        $brand['endpoint_brand_id'] = 1;
        $brand->save();

        $productLine = new EndpointProductLine();
        $productLine->EndpointBrand = $brand;
        $productLine['short_name'] = 'aap9xxx6xxx';
        $productLine['long_name'] = '9xxx and 6xxx line';
        $productLine['endpoint_product_line_id'] = 1;
        $productLine['cfg_dir'] = '';
        $productLine['cfg_ver'] = '';
        $productLine['firmware_vers'] = '';
        $productLine['firmware_files'] = '';
        $productLine['special_cfgs'] = '';
        $productLine->save();

        $model = new EndpointModel();
        $model->EndpointProductLine = $productLine;
        $model['name'] = '6755i';
        $model['template_list'] = '';
        $model['template_data'] = '';
        $model['endpoint_model_id'] = 1;
        $model->save();
    }
}
