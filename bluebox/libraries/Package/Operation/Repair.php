<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation_Repair extends Package_Operation
{
    public static function execute($args)
    {
        if (!is_array($args))
        {
            $args = array($args);
        }

        if (empty($args[0]))
        {
            throw new Package_Operation_Exception('Repair requires the package identifier to repair');
        }

        $identifier = $args[0];

        kohana::log('debug', 'Starting repair of ' .$identifier);

        Package_Operation_Verify::execute($identifier);

        self::repair($identifier);

        $metadata = &Package_Catalog::getPackageByIdentifier($identifier);

        unset($metadata['datastore_id']);

        Package_Catalog_Datastore::export($metadata);
    }

    protected static function repair($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->repair($identifier);
    }
}