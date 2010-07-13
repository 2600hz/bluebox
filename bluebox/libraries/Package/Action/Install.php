<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Action_Install extends Package_Action
{
    public static function execute($args)
    {
        if (!is_array($args))
        {
            $args = array($args);
        }
        
        if (empty($args[0]))
        {
            throw new Package_Action_Exception('Install requires the package identifier to install');
        }

        $identifier = $args[0];

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
            throw new Package_Action_Exception('Install is not a sane action for a package with status ' .$package['status']);
        }

        Package_Action_Verify::execute($identifier);
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