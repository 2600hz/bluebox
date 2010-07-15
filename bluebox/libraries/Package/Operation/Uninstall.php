<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation_uninstall extends Package_Operation
{
    protected function validate($identifier)
    {
        if ($package['status'] != Package_Manager::STATUS_INSTALLED)
        {
            throw new Package_Operation_Exception('Uninstall is not a sane operation for a package with status ' .$package['status']);
        }

        Package_Dependency::validateAbandon($identifier);
    }

    protected function preExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preUninstall($identifier);
    }

    protected function exec($identifier)
    {
        kohana::log('debug', 'Starting uninstall of ' .$identifier);
        
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->uninstall($identifier);
    }

    protected function postExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postUninstall($identifier);
    }

    protected function finalize($identifier)
    {
        $metadata = &Package_Catalog::getPackageByIdentifier($identifier);
        
        Package_Catalog_Datastore::remove($metadata);
    }
}