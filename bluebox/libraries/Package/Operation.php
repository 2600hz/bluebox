<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Operation
{
    public static function dispatch($operation, $identifiers)
    {
        if (!is_array($identifiers))
        {
            $identifiers = array($identifiers);
        }

        $steps = array('validate', 'preExec', 'exec', 'postExec', 'finalize');

        switch ($operation)
        {
            case 'verify':
                $agent = new Package_Operation_Verify;

                break;

            case 'repair':
                $agent = new Package_Operation_Repair;

                break;

            case 'install':
                $agent = new Package_Operation_Install;

                break;

            case 'uninstall':
                $agent = new Package_Operation_Uninstall;

                break;            

            case 'migrate':
                $agent = Package_Operation_Migrate;

                break;

            default:
                throw new Package_Operation_Exception('Unknown operation ' .$operation);
        }

        foreach($identifiers as $identifier)
        {
            $name = Package_Catalog::getPackageName($identifier);

            kohana::log('debug', 'Package management dispatching ' .$operation .' on ' .$identifier .'(' .$name .')');
        }

        foreach($steps as $step)
        {
            foreach($identifiers as $pos => $identifier)
            {
                try
                {
                    kohana::log('debug', 'Package management executing ' .get_class($agent) .'->' .$step .'(' .$identifier .')');

                    self::execStep($identifier, $step, $agent);
                }
                catch (Exception $e)
                {
                    // TODO: This needs to also stop anything depending on it during
                    // install or uninstall.
                    unset($identifiers[$pos]);

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

    protected static function locatePackageSource($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (empty($package['directory']))
        {
            if (empty($package['sourceURL']))
            {
                throw new Package_Operation_Exception('Migrate could not find the source for the package');
            }

            Package_Import::package($package['sourceURL']);
        }
    }

    protected static function rollback($operation, $identifier, $step, $error)
    {
        kohana::log('error', 'Package operation ' .$operation .' failed during ' .$step .' on package ' .$identifier .': ' .$error->getMessage());

        throw $error;
    }
}