<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Action
{
    public static function dispatch($action)
    {
        $args = func_get_args();

        array_shift($args);

        switch (strtolower($action))
        {
            case 'verify':
                return Package_Action_Verify::execute($args);

            case 'repair':
                return Package_Action_Repair::execute($args);
            
            case 'install':
                return Package_Action_Install::execute($args);

            case 'uninstall':
                return Package_Action_Uninstall::execute($args);

            case 'migrate':
                return Package_Action_Migrate::execute($args);

            default:
                throw new Package_Action_Exception('Unknown action ' .$action);
        }
    }

    protected static function finalize($identifier, $action = NULL)
    {
        $metadata = &Package_Catalog::getPackageByIdentifier($identifier);

        switch ($action)
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