<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Action_Migrate extends Package_Action
{
    public static function execute($args)
    {
        if (empty($args[0]))
        {
            throw new Package_Action_Exception('Migrate requires the package identifier to migrate');
        }

        $identifier = $args[0];

        $packageName = Package_Catalog::getPackageName($identifier);

        $from = Package_Catalog::getInstalledPackage($packageName);

        if(empty($from))
        {
            throw new Package_Action_Exception('Migrate could not determine the package that is being updated');
        }

        $from = $from['identifier'];

        self::verify($identifier, $from);

        kohana::log('debug', 'Starting migrate of ' .$from .' to ' .$identifier);

        try
        {
            self::preMigrate($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'preMigrate', $e);
        }

        try
        {
            self::migrate($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'migrate', $e);
        }

        try
        {
            self::postMigrate($identifier);
        }
        catch (Exception $e)
        {
            self::rollback($identifier, 'postMigrate', $e);
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

    protected static function verify($identifier, $from)
    {
        $package = Package_Catalog::getPackageByIdentifier($from);

        if ($package['status'] != Package_Manager::STATUS_INSTALLED)
        {
            throw new Package_Action_Exception('Migrate is not a sane action for a package with status ' .$package['status']);
        }

        Package_Action_Verify::execute($identifier);
    }

    protected static function preMigrate($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preMigrate();
    }

    protected static function migrate($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->migrate();
    }

    protected static function postMigrate($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postMigrate();
    }

    protected static function finalize($identifier)
    {
        parent::finalize($identifier, 'migrate');
    }
}