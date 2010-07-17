<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Operation
{
    public static function dispatch($operation)
    {
        $args = func_get_args();

        array_shift($args);

        switch (strtolower($operation))
        {
            case 'verify':
                return Package_Operation_Verify::execute($args);

            case 'repair':
                return Package_Operation_Repair::execute($args);
            
            case 'install':
                return Package_Operation_Install::execute($args);

            case 'uninstall':
                return Package_Operation_Uninstall::execute($args);

            case 'migrate':
                return Package_Operation_Migrate::execute($args);

            default:
                throw new Package_Operation_Exception('Unknown operation ' .$operation);
        }
    }

    protected static function finalize($identifier, $operation = NULL)
    {
        $metadata = &Package_Catalog::getPackageByIdentifier($identifier);

        switch ($operation)
        {
            case 'install':
                $metadata['status'] = Package_Manager::STATUS_INSTALLED;

                Package_Catalog_Datastore::export($metadata);

                break;

            case 'uninstall':
                Package_Catalog_Datastore::remove($metadata);

                break;

            case 'migrate':
                $metadata['status'] = Package_Manager::STATUS_INSTALLED;
            
                Package_Catalog_Datastore::export($metadata);

                break;

            default:
                break;
        }
    }

    protected static function rollback($identifier, $failed_step, $error)
    {
        throw $error;
    }
}