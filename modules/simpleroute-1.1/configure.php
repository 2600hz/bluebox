<?php defined('SYSPATH') or die('No direct access allowed.');

class SimpleRoute_1_1_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'simpleroute';
    public static $displayName = 'Simple Route';
    public static $author = 'K Anderson';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Simple Route';
    public static $description = 'A simplified routing mechanism for trunk modules';
    public static $default = true;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'trunkmanager' => 0.1
    );
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'simpleroute/index';
    public static $navSubmenu = array(
        'Simple Routes' => '/simpleroute/index',
        'Add Route' => '/simpleroute/create',
        'Edit Route' => array(
            'url' => '/simpleroute/edit',
            'disabled' => true
        ) ,
        'Delete Route' => array(
            'url' => '/simpleroute/delete',
            'disabled' => true
        )
    );
    
    public function postInstall()
    {
        Doctrine::getTable('SimpleRoute')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
        foreach (SimpleRouteLib::importConfigRoutes() as $route)
        {
            $simpleRoute = new SimpleRoute;

            $simpleRoute->fromArray($route);

            $simpleRoute['account_id'] = 1;

            $simpleRoute->save();
        }
    Doctrine::getTable('SimpleRoute')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
    }

    public function migrate()
    {
        $conn = Doctrine_Manager::connection();

        if (!$conn->import->tableExists('simple_route'))
        {
            Doctrine::createTablesFromArray(array('SimpleRoute'));

            $this->postInstall();
        }
    }
}
