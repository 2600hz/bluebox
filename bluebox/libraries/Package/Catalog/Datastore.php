<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Catalog_Datastore extends Package_Catalog
{
    public static function import(&$metadata)
    {
        $possibleModels = glob($metadata['directory'] . '/models/*.php', GLOB_MARK);

        if (!empty($possibleModels))
        {
            $metadata['models'] = Doctrine::loadModels($metadata['directory'] . '/models', Doctrine::MODEL_LOADING_CONSERVATIVE);
        }
        else
        {
            $metadata['models'] = array();
        }

        try
        {
            $package = Doctrine::getTable('Package')->findOneByBasedir($metadata['basedir']);

            if (!$package)
            {
                return;
            }

            $metadata['status'] = $package['status'];

            $metadata['datastore_id'] = $package['package_id'];
        }
        catch(Exception $e)
        {
        }
    }

    public static function export(&$metadata)
    {
        $package = Doctrine::getTable('Package')->findOneByName($metadata['packageName']);

        if (!$package)
        {
            kohana::log('debug', 'Creating new package entry for ' .$metadata['packageName'] .'@' .$metadata['basedir']);

            $package = new Package();
        }
        else
        {
            kohana::log('debug', 'Updating package entry for ' .$metadata['packageName'] .'@' .$metadata['basedir'] .' (' .$package['package_id'] .')');
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

        if (!empty($metadata['models']))
        {
            self::integrateNumberType($metadata['models'], $metadata['datastore_id']);
        }
    }

    public static function remove(&$metadata)
    {
        $package = Doctrine::getTable('Package')->findOneByName($metadata['packageName']);

        if ($package)
        {
            kohana::log('debug', 'Remove package entry for ' .$metadata['packageName'] .'@' .$metadata['basedir'] .' (' .$package['package_id'] .')');

            $package->delete();
        }

        if (!empty($metadata['models']))
        {
            self::removeNumberType($metadata['models']);
        }
    }

    protected static function integrateNumberType($models, $datastore_id)
    {
        foreach($models as $model)
        {
            if (!class_exists($model) || !is_subclass_of($model, 'Number'))
            {
                continue;
            }

            Kohana::log('debug', 'Adding ' . $model . ' to NumberType');

            try
            {
                $numberType = Doctrine::getTable('NumberType')->findOneByClass($model);

                if (!$numberType)
                {
                    Kohana::log('debug', 'Could not find ' . $model . ' in NumberType, adding as new number type');

                    $numberType = new NumberType();

                    $numberType['class'] = $model;
                }

                $numberType['package_id'] = $datastore_id;

                $numberType->save();

                $numberType->free(TRUE);

            } 
            catch(Exception $e)
            {
                throw new Package_Catalog_Exception($e->getMessage() .print_r($models, true));

                return FALSE;
            }
        }

        return TRUE;
    }

    protected static function removeNumberType($models)
    {
        foreach($models as $model)
        {
            if (!class_exists($model) || !is_subclass_of($model, 'Number'))
            {
                continue;
            }

            Kohana::log('debug', 'Removing ' . $model . ' from NumberType after package uninstall');

            try
            {
                $numberType = Doctrine::getTable('NumberType')->findOneByClass($model);

                if ($numberType)
                {
                    $numberType->delete();

                    $numberType->free(TRUE);
                }

            } 
            catch(Exception $e)
            {
                throw new Package_Catalog_Exception($e->getMessage());

                return FALSE;
            }
        }

        return TRUE;
    }
}