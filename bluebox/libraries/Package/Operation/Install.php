<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation_Install extends Package_Operation
{
    protected function validate($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (empty($package['directory']))
        {
            if (empty($package['sourceURL']))
            {
                throw new Package_Operation_Exception('Install could not find the source for the package');
            }

            Package_Import::package($package['sourceURL']);

            $package = Package_Catalog::getPackageByIdentifier($identifier);
        }

        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if ($package['status'] != Package_Manager::STATUS_UNINSTALLED)
        {
            throw new Package_Operation_Exception('Install is not a sane operation for a package with status ' .$package['status']);
        }

        Package_Operation_Verify::exec($identifier);
    }

    protected function preExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preInstall($identifier);
    }

    protected function exec($identifier)
    {
        kohana::log('debug', 'Starting install of ' .$identifier);

        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->install($identifier);
    }

    protected function postExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postInstall($identifier);
    }

    protected function finalize($identifier)
    {
        $metadata = &Package_Catalog::getPackageByIdentifier($identifier);

        Package_Catalog_Datastore::remove($metadata);
    }

    public static function execute($args)
    {
        if (!is_array($args))
        {
            $args = array($args);
        }
        
        if (empty($args[0]))
        {
            throw new Package_Operation_Exception('Install requires the package identifier to install');
        }

        $identifier = $args[0];

        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (empty($package['directory']))
        {
            if (empty($package['sourceURL']))
            {
                throw new Package_Operation_Exception('Install could not find the source for the package');
            }

            Package_Import::package($package['sourceURL']);

            $package = Package_Catalog::getPackageByIdentifier($identifier);
        }

        self::verify($identifier);

        kohana::log('debug', 'Starting install of ' .$identifier);

        try
        {
            self::preInstall($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'preInstall', $e);
        }

        try
        {
            self::install($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'install', $e);
        }        

        try
        {
            self::postInstall($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'postInstall', $e);
        }

        try
        {
            self::finalize($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'finalize', $e);
        }
    }

    protected static function verify($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if ($package['status'] != Package_Manager::STATUS_UNINSTALLED)
        {
            throw new Package_Operation_Exception('Install is not a sane operation for a package with status ' .$package['status']);
        }

        Package_Operation_Verify::execute($identifier);
    }

    protected static function preInstall($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preInstall($identifier);
    }

    protected static function install($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->install($identifier);
    }

    protected static function postInstall($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postInstall($identifier);
    }

    protected static function finalize($identifier)
    {
        parent::finalize($identifier, 'install');
    }
}