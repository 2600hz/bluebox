<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Operation_Migrate extends Package_Operation
{
    public function validate($identifier)
    {
        self::locatePackageSource($identifier);

        $package = Package_Catalog::getPackageByIdentifier($identifier);

        $from = Package_Catalog::getInstalledPackage($package['packageName']);

        if(empty($from))
        {
            throw new Package_Operation_Exception('Migrate could not determine the package that is being updated');
        }

        $from = $from['identifier'];

        kohana::log('debug', 'Package management executing Package_Operation_Verify::exec(' .$identifier .')');

        Package_Operation_Verify::exec($identifier);
    }

    public function preExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preMigrate($identifier);
    }

    public function exec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->migrate($identifier);
    }

    public function postExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postMigrate($identifier);
    }

    public function finalize($identifier)
    {
        $package = &Package_Catalog::getPackageByIdentifier($identifier);

        $package['status'] = Package_Manager::STATUS_INSTALLED;

        Package_Catalog_Datastore::export($package);
    }
}