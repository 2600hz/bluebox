<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Catalog_Datastore extends Package_Catalog
{
    public static function import(&$metadata)
    {
        $package = Doctrine::getTable('Package')->findOneByBasedir($metadata['basedir']);

        if (!$package)
        {
            return;
        }

        $metadata['status'] = $package['status'];

        $metadata['datastore_id'] = $package['package_id'];
        
        $possibleModels = glob($metadata['directory'] . '/models/*.php', GLOB_MARK);

        if (!empty($possibleModels))
        {
            $metadata['models'] = Doctrine::loadModels($metadata['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
        }
        else
        {
            $metadata['models'] = array();
        }
    }

    public static function export(&$metadata)
    {
        $package = Doctrine::getTable('Package')->findOneByName($metadata['packageName']);

        if (!$package)
        {
            $package = new Package();
        }

        $registryIgnoreKeys = array_flip(array(
            'packageName',
            'displayName',
            'version',
            'packageStatus',
            'directory',
            'configure_instance',
            'navStructures',
            'datastore_id',
            'type',
            'upgrades',
            'downgrades',
            'basedir',
            'status',
            'models'
        ));

        $package['name'] = $metadata['packageName'];

        $package['display_name'] = $metadata['displayName'];

        $package['version'] = $metadata['version'];

        $package['type'] = $metadata['type'];

        $package['status'] = $metadata['status'];

        $package['basedir'] = $metadata['basedir'];

        $package['navigation'] = $metadata['navStructures'];

        $package['registry'] = array_diff_key($metadata, $registryIgnoreKeys);

        $package->save();

        $metadata['datastore_id'] = $package['package_id'];
    }

    public static function remove(&$metadata)
    {
        $package = Doctrine::getTable('Package')->findOneByName($metadata['packageName']);

        if (!$package)
        {
            return;
        }

        $package->delete();
    }
}