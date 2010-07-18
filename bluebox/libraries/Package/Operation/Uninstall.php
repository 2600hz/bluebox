<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Operation_uninstall extends Package_Operation
{
    public function validate($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);
        
        Package_Dependency::validateAbandon($identifier);
    }

    public function preExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->preUninstall($identifier);
    }

    public function exec($identifier)
    {   
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->uninstall($identifier);
    }

    public function postExec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->postUninstall($identifier);
    }

    public function finalize($identifier)
    {
        $metadata = &Package_Catalog::getPackageByIdentifier($identifier);
        
        Package_Catalog_Datastore::remove($metadata);
    }
}