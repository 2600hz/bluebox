<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Catalog
{
    protected static $catalog;

    protected static $packageList;

    protected static $configureCache = array();

    public static function buildCatalog()
    {
        self::$catalog = array();

        self::$packageList = array();

        $configureFiles = self::listConfigureFiles();

        $installedPackages = array();

        // Process each of the known package configuration classes
        foreach($configureFiles as $class => $filepath)
        {
            $metadata = get_class_vars($class);

            $metadata['configure_class'] = $class;

            Package_Catalog_Standardize::packageData($metadata, $filepath);

            Package_Catalog_Standardize::navigation($metadata);

            Package_Catalog_Standardize::typeRestrictions($metadata);

            Package_Catalog_Datastore::import($metadata);

            self::$catalog[$metadata['identifier']] = $metadata;

            self::$packageList[$metadata['packageName']][$metadata['status']][$metadata['identifier']]
                = &self::$catalog[$metadata['identifier']];

            $installedPackages[$metadata['identifier']] = $metadata['packageName'];
        }

        foreach ($installedPackages as $identifier => $name)
        {
            $installedPackage = &self::$catalog[$identifier];

            foreach (self::$packageList[$name] as $status => $packages)
            {
                if ($status == Package_Manager::STATUS_INSTALLED)
                {
                    continue;
                }

                foreach ($packages as $key => $package)
                {
                    if (Package_Dependency::compareVersion($package['version'], $installedPackage['version']))
                    {
                        $installedPackage['upgrades'][$package['version']] = $key;
                    }
                    else
                    {
                        $installedPackage['downgrades'][$package['version']] = $key;
                    }
                }

                if (!empty($installedPackage['upgrades']))
                {
                    uksort($installedPackage['upgrades'], array('Package_Dependency', 'compareVersion'));

                    $installedPackage['upgrades'] = array_reverse($installedPackage['upgrades']);
                }

                if (!empty($installedPackage['downgrades']))
                {
                    uksort($installedPackage['downgrades'], array('Package_Dependency', 'compareVersion'));

                    $installedPackage['downgrades'] = array_reverse($installedPackage['downgrades']);
                }
            }
        }

        return self::$catalog;
    }

    public static function getCatalog()
    {
        self::init();

        return self::$catalog;
    }


    public static function getPackageList()
    {
        self::init();

        return self::$packageList;
    }

    public static function getAvaliableVersions($name)
    {
        self::init();
        
        $avaliableVersions = array();

        if (!isset(self::$packageList[$name]))
        {
            throw new Package_Catalog_Exception('Unknown package name ' .$name);
        }

        foreach (self::$packageList[$name] as $status => $packages)
        {
            if ($status == Package_Manager::STATUS_INSTALLED)
            {
                continue;
            }

            foreach($packages as $key => $package)
            {
                $avaliableVersions[$key] = $package['version'];
            }
        }

        uksort($avaliableVersions, array('Package_Dependency', 'compareVersion'));

        return array_reverse($avaliableVersions);
    }

    public static function getPackageByName($name)
    {
        self::init();

        if (!isset(self::$packageList[$name]))
        {
            throw new Package_Catalog_Exception('Unknown package name ' .$name);
        }

        return self::$packageList[$name];
    }

    public static function getInstalledPackage($name)
    {
        self::init();

        if (!isset(self::$packageList[$name][Package_Manager::STATUS_INSTALLED]))
        {
            return FALSE;
        }

        $packages = self::$packageList[$name][Package_Manager::STATUS_INSTALLED];

        $metadata = reset($packages);

        $identifier = key($packages);

        return $metadata;
    }
    
    public static function getPackageByIdentifier($identifier)
    {
        self::init();

        if (!isset(self::$catalog[$identifier]))
        {
            throw new Package_Catalog_Exception('Unknown package identifier ' .$identifier);
        }

        return self::$catalog[$identifier];
    }

    public static function getPackageName($identifier)
    {
        $package = self::getPackageByIdentifier($identifier);

        return $package['packageName'];
    }

    public static function getPackageDisplayName($identifier)
    {
        $package = self::getPackageByIdentifier($identifier);

        return $package['displayName'];
    }

    public static function getPackageConfigureInstance($identifier)
    {
        self::init();

        if (!isset(self::$catalog[$identifier]))
        {
            throw new Package_Catalog_Exception('Unknown package identifier ' .$identifier);
        }

        $package = &self::$catalog[$identifier];

        if (isset($package['configure_instance']))
        {
            return $package['configure_instance'];
        }

        return $package['configure_instance'] = new $package['configure_class'];
    }

    protected static function init()
    {
        if (empty(self::$packageList))
        {
            self::buildCatalog();
        }
    }

    protected static function listConfigureFiles()
    {
        $configureFiles = glob(MODPATH . '*/configure.php', GLOB_MARK);

        if(empty(self::$configureCache))
        {
            array_unshift($configureFiles, APPPATH . 'configure.php');
        }
        
        foreach($configureFiles as $configureFile)
        {
            require_once ($configureFile);

            $declaredAfter = get_declared_classes();

            $foundClass = end($declaredAfter);

            if ($foundClass && is_subclass_of($foundClass, 'Bluebox_Configure'))
            {
                self::$configureCache[$foundClass] = $configureFile;
            }
        }
        
        return self::$configureCache;
    }
}