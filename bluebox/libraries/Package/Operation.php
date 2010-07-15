<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation
{
    public static function dispatch($operation, $identifiers)
    {
        switch ($operation)
        {
            case 'verify':
                $agent = new Package_Operation_Verify;

                $steps = array('exec');

                break;

            case 'repair':
                $agent = new Package_Operation_Repair;

                $steps = array('validate', 'exec', 'finalize');

                break;

            case 'install':
                $agent = new Package_Operation_Install;

                $steps = array('validate', 'preExec', 'exec', 'postExec', 'finalize');

                break;

            case 'uninstall':
                $agent = new Package_Operation_Uninstall;

                $steps = array('validate', 'preExec', 'exec', 'postExec', 'finalize');

                break;            

            case 'migrate':
                $agent = Package_Operation_Migrate;

                $steps = array('validate', 'exec', 'finalize');

                break;

            default:
                throw new Package_Operation_Exception('Unknown operation ' .$operation);
        }

        foreach($steps as $step)
        {
            foreach($identifiers as $identifier)
            {
                try
                {
                    self::execStep($identifier, $step, $agent);
                }
                catch (Exception $e)
                {
                    unset($identifiers[$identifier]);

                    self::rollback($operation, $identifier, $step, $e);
                }
            }
        }
    }

    protected static function execStep($identifier, $step, $agent)
    {
        switch($step)
        {
            case 'validate':
                $agent->validate($identifier);

                break;

            case 'preExec':
                $agent->preExec($identifier);

                break;

            case 'exec':
                $agent->exec($identifier);

                break;

            case 'postExec':
                $agent->postExec($identifier);

                break;

            case 'finalize':
                $agent->finalize($identifier);
            
                break;

            default:
                throw new Package_Operation_Exception('Unknown step ' .$step);
        }
    }

    protected static function finalize($operation, $identifier)
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

    protected static function rollback($operation, $identifier, $step, $error)
    {
        throw $error;
    }
}