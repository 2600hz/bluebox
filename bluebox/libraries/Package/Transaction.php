<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Transaction
{
    protected $transaction = array();

    public static function beginTransaction()
    {
        return new Package_Transaction();
    }

    public function install($identifier)
    {
        $this->transaction[Package_Manager::OPERATION_INSTALL][$identifier]
            = $identifier;
    }

    public function uninstall($identifier)
    {
        $this->transaction[Package_Manager::OPERATION_UNINSTALL][$identifier]
                = $identifier;
    }

    public function verify($identifier)
    {
        $this->transaction[Package_Manager::OPERATION_VERIFY][$identifier]
                = $identifier;
    }

    public function repair($identifier)
    {
        $this->transaction[Package_Manager::OPERATION_REPAIR][$identifier]
                = $identifier;
    }

    public function migrate($identifier)
    {
        $this->transaction[Package_Manager::OPERATION_MIGRATE][$identifier]
                = $identifier;
    }

    public function commit()
    {
        foreach ($this->transaction as $operation => $identifiers)
        {
            if ($operation == Package_Manager::OPERATION_UNINSTALL)
            {
                kohana::log('debug', 'Sorting package ' .$operation .' transaction list by dependencies');

                Package_Transaction_Graph::determineUninstallOrder();

                usort($identifiers, array('Package_Transaction_Graph', 'sortUninstall'));
            }
            else
            {
                kohana::log('debug', 'Sorting package ' .$operation .' transaction list by requirements');

                Package_Transaction_Graph::determineInstallOrder();

                usort($identifiers, array('Package_Transaction_Graph', 'sortInstall'));
            }

            Package_Operation::dispatch($operation, $identifiers);
        }
    }
}