<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Action_uninstall extends Package_Action
{
    public static function execute($args)
    {
        if (!is_array($args))
        {
            $args = array($args);
        }

        if (empty($args[0]))
        {
            throw new Package_Action_Exception('uninstall requires the package identifier to uninstall');
        }

        $identifier = $args[0];

        Package_Dependency::validateAbandon($identifier);
        
        kohana::log('debug', 'Starting uninstall of ' .$identifier);

        try
        {
            self::preUninstall($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'preUninstall', $e);
        }

        try
        {
            self::uninstall($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'uninstall', $e);
        }

        try
        {
            self::postUninstall($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'postUninstall', $e);
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
    
    protected static function preUninstall($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preUninstall($identifier);
    }

    protected static function uninstall($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->uninstall($identifier);
    }

    protected static function postUninstall($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postUninstall($identifier);
    }

    protected static function finalize($identifier)
    {
        parent::finalize($identifier, 'uninstall');
    }
}