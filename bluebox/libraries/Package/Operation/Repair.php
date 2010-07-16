<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation_Repair extends Package_Operation
{
    public function validate($identifier)
    {
        kohana::log('debug', 'Package management executing Package_Operation_Verify::exec(' .$identifier .')');

        // TODO: If this failes find unistall all dependent packages
        Package_Operation_Verify::exec($identifier);
    }

    public function preExec($identifier)
    {
        
    }

    public function exec($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->repair($identifier);
    }

    public function postExec($identifier)
    {

    }

    public function finalize($identifier)
    {
        $package = &Package_Catalog::getPackageByIdentifier($identifier);

        unset($package['datastore_id']);

        Package_Catalog_Datastore::export($package);
    }
}