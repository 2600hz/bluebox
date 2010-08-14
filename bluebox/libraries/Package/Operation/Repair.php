<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Operation_Repair extends Package_Operation
{
    public function validate($identifier)
    {
        Package_Message::log('debug', 'Package management executing Package_Operation_Verify::exec(' .$identifier .')');

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