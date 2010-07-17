<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Operation_Migrate extends Package_Operation
{
    public static function execute($args)
    {
        if (empty($args[0]))
        {
            throw new Package_Operation_Exception('Migrate requires the package identifier to migrate');
        }

        $identifier = $args[0];

        $package = Package_Catalog::getPackageByIdentifier($identifier);

        $from = Package_Catalog::getInstalledPackage($package['packageName']);

        if(empty($from))
        {
            throw new Package_Operation_Exception('Migrate could not determine the package that is being updated');
        }

        $from = $from['identifier'];

        if (empty($package['directory']))
        {
            if (empty($package['sourceURL']))
            {
                throw new Package_Operation_Exception('Migrate could not find the source for the package');
            }

            Package_Import::package($package['sourceURL']);

            $package = Package_Catalog::getPackageByIdentifier($identifier);
        }

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
            throw new Package_Operation_Exception('Migrate is not a sane operation for a package with status ' .$package['status']);
        }

        Package_Operation_Verify::execute($identifier);
    }

    protected static function preMigrate($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preMigrate($identifier);
    }

    protected static function migrate($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->migrate($identifier);
    }

    protected static function postMigrate($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postMigrate($identifier);
    }

    protected static function finalize($identifier)
    {
        parent::finalize($identifier, 'migrate');
    }
}