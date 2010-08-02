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
        $outboundPatterns = kohana::config('simpleroute.outbound_patterns');

        if (!is_array($outboundPatterns))
        {
            return;
        }

        // This is the second work arround for the double loading issue... hmmm
        $createdPatterns = array();
        
        foreach ($outboundPatterns as $outboundPattern)
        {
            if (empty($outboundPattern['name']))
            {
                continue;
            }

            if (in_array($outboundPattern['name'], $createdPatterns))
            {
                continue;
            }

            $createdPatterns[] = $outboundPattern['name'];

            if (empty($outboundPattern['patterns']))
            {
                continue;
            }

            if (!is_array($outboundPattern['patterns']))
            {
                $outboundPattern['patterns'] = array($outboundPattern['patterns']);
            }

            $simpleRoute = new SimpleRoute;

            $simpleRoute['name'] = $outboundPattern['name'];

            $simpleRoute['patterns'] = $outboundPattern['patterns'];

            $simpleRoute->save();
        }
    }

    public function migrate()
    {
        Doctrine::createTablesFromArray(array('SimpleRoute'));
    }
}