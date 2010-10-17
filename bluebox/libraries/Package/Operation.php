<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
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
            case Package_Manager::OPERATION_VERIFY:
                $agent = new Package_Operation_Verify;

                break;

            case Package_Manager::OPERATION_REPAIR:
                $agent = new Package_Operation_Repair;

                break;

            case Package_Manager::OPERATION_INSTALL:
                $agent = new Package_Operation_Install;

                break;

            case Package_Manager::OPERATION_UNINSTALL:
                $agent = new Package_Operation_Uninstall;

                break;            

            case Package_Manager::OPERATION_MIGRATE:
                $agent = new Package_Operation_Migrate;

                break;

            default:
                throw new Package_Operation_Exception('Unknown operation ' .$operation);
        }

        foreach($identifiers as $identifier)
        {
            $name = Package_Catalog::getPackageName($identifier);

            Package_Message::log('debug', 'Package management dispatching ' .$operation .' on ' .$identifier .'(' .$name .')');
        }

        $successfull = TRUE;

        foreach($steps as $step)
        {
            foreach($identifiers as $pos => $identifier)
            {
                try
                {
                    Package_Message::log('debug', 'Package management executing ' .get_class($agent) .'->' .$step .'(' .$identifier .')');

                    self::execStep($identifier, $step, $agent);
                }
                catch (Exception $e)
                {
                    // TODO: This needs to also stop anything depending on it during
                    // install or uninstall.
                    unset($identifiers[$pos]);

                    self::rollback($operation, $identifier, $step, $e);

                    $successfull = FALSE;
                }
            }
        }

        foreach($identifiers as $identifier)
        {
            $package = Package_Catalog::getPackageByIdentifier($identifier);

            Package_Operation_Message::set(ucfirst($operation) .' of package ' .$package['displayName'] .' version ' .$package['version'] .' completed', 'success', $identifier);
        }

        return $successfull;
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
                throw new Package_Operation_Exception('Unknown step ' .$step, $identifier);
        }
    }

    protected static function locatePackageSource($identifier)
    {
        $package = Package_Catalog::getPackageByIdentifier($identifier);

        if (empty($package['directory']))
        {
            if (empty($package['sourceURL']))
            {
                throw new Package_Operation_Exception('Migrate could not find the source for the package', $identifier);
            }

            Package_Import::package($package['sourceURL']);
        }
    }

    protected static function rollback($operation, $identifier, $step, $error)
    {
        Package_Message::log('error', 'Package operation ' .$operation .' failed during ' .$step .' on package ' .$identifier .': ' .$error->getMessage());

        if ($step == 'validate')
        {
            Package_Message::log('debug', 'No rollback action for ' .$operation .' if we dont get past validate on package ' .$identifier);

            Package_Operation_Message::set($error->getMessage(), 'error', $identifier);

            return;
        }

        Package_Operation_Message::set($error->getMessage(), 'error', $identifier);

        switch ($operation)
        {
            case Package_Manager::OPERATION_INSTALL:
                try
                {
                    Package_Message::log('debug', 'Trying to rollback install via uninstall on package ' .$identifier);

                    Package_Operation_Message::ignoreLogLevels('success');

                    self::dispatch(Package_Manager::OPERATION_UNINSTALL, $identifier);

                    Package_Operation_Message::acceptAllLogLevels();
                }
                catch (Exception $e)
                {
                    Package_Operation_Message::set('Error during rollback: ' .$e->getMessage(), 'alert', $identifier);
                }

                break;

            case Package_Manager::OPERATION_MIGRATE:
                try
                {
                    if($package = Package_Catalog::getPackageByIdentifier($identifier))
                    {
                        if ($installed = Package_Catalog::getInstalledPackage($package['packageName']))
                        {
                            Package_Message::log('debug', 'Trying to rollback migrate via repair on package ' .$installed['identifier']);

                            Package_Operation_Message::ignoreLogLevels('success');

                            if(self::dispatch(Package_Manager::OPERATION_REPAIR, $installed['identifier']))
                            {
                                Package_Operation_Message::set('Rollback of package ' .$installed['displayName'] .' version ' .$installed['version'] .' completed', 'info', $identifier);
                            }

                            Package_Operation_Message::acceptAllLogLevels();
                        }
                    }
                }
                catch (Exception $e)
                {
                    Package_Operation_Message::set('Error during rollback: ' .$e->getMessage(), 'alert', $identifier);
                }

                break;

            case Package_Manager::OPERATION_UNINSTALL:
            case Package_Manager::OPERATION_REPAIR:
            case Package_Manager::OPERATION_VERIFY:
            default:
                Package_Message::log('debug', 'No rollback action for ' .$operation .' on package ' .$identifier);

                break;
        }
    }
}