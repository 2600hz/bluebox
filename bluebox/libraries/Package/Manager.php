<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Manager
{
    const STATUS_UNINSTALLED = 'uninstalled';
    const STATUS_INSTALLED = 'installed';

    const OPERATION_MIGRATE = 'migrate';
    const OPERATION_INSTALL = 'install';
    const OPERATION_REPAIR = 'repair';
    const OPERATION_VERIFY = 'verify';
    const OPERATION_UNINSTALL = 'uninstall';

    const TYPE_DEFAULT = 'unknown';
    const TYPE_CORE = 'core';
    const TYPE_MODULE = 'module';
    const TYPE_PLUGIN = 'plugin';
    const TYPE_DRIVER = 'driver';
    const TYPE_SERVICE = 'service';
    const TYPE_DIALPLAN = 'dialplan';
    const TYPE_ENDPOINT = 'endpoint';
    const TYPE_SKIN = 'skin';

    public static function getDisplayList()
    {
        $packageList = Package_Catalog::getPackageList();

        $avaliablePackages = array(
            'Upgrade Avaliable' => array(),
            'Installed' => array(),
            'Uninstalled' => array()
        );
            
        foreach ($packageList as $name => $avaliable)
        {
            if (isset($avaliable[self::STATUS_INSTALLED]))
            {
                $packages = $avaliable[self::STATUS_INSTALLED];

                $package = reset($packages);

                if (!empty($package['upgrades']))
                {
                    $avaliablePackages['Upgrade Avaliable'][$name] = $package;
                }
                else
                {
                    $avaliablePackages['Installed'][$name] = $package;
                }
                
                continue;
            }

            $newestPackage = array();

            foreach ($avaliable as $status => $packages)
            {
                foreach($packages as $key => $package)
                {
                    if(empty($newestPackage['version']))
                    {
                        $newestPackage = $package;

                        continue;
                    }

                    if (Package_Dependency::compareVersion($package['version'], $newestPackage['version']))
                    {
                        $newestPackage = $package;
                    }
                }
            }

            if (!empty($newestPackage))
            {
                $avaliablePackages['Uninstalled'][$name] = $newestPackage;
            }
        }
        
        return $avaliablePackages;
    }
}
