<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation_Install extends Package_Operation
{
    public function validate($identifier)
    {
        self::locatePackageSource($identifier);

        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if ($package['status'] != Package_Manager::STATUS_UNINSTALLED)
        {
            throw new Package_Operation_Exception('Install is not a sane operation for a package with status ' .$package['status']);
        }

        kohana::log('debug', 'Package management executing Package_Operation_Verify::exec(' .$identifier .')');

        Package_Operation_Verify::exec($identifier);
    }

    public function preExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preInstall($identifier);
    }

    public function exec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->install($identifier);
    }

    public function postExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postInstall($identifier);
    }

    public function finalize($identifier)
    {
        $package = &Package_Catalog::getPackageByIdentifier($identifier);
        
        $package['status'] = Package_Manager::STATUS_INSTALLED;

        Package_Catalog_Datastore::export($package);
    }
}