<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Transaction
{
    protected $transaction = array();

    protected static $instance = NULL;

    public static function beginTransaction()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new Package_Transaction();
        }
        
        self::$instance->transaction = array();

        return self::$instance;
    }

    public static function getTransaction()
    {
        return self::$instance;
    }

    public static function checkTransaction($name, $operation = NULL)
    {
        $transaction = self::$instance->transaction;

        if (is_null($operation))
        {
            $operation = Package_Manager::OPERATION_INSTALL;
        }

        if (empty($transaction[$operation]))
        {
            throw new Package_Transaction_Exception('Transcation contains no packages for that operation');
        }

        foreach($transaction[$operation] as $identifier)
        {
            $package = Package_Catalog::getPackageByIdentifier($identifier);

            if ($package['packageName'] == $name)
            {
                return $package;
            }
        }

        throw new Package_Transaction_Exception('Transcation does not containt package');
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

                Package_Dependency_Graph::determineUninstallOrder();

                usort($identifiers, array('Package_Dependency_Graph', 'sortUninstall'));
            }
            else
            {
                kohana::log('debug', 'Sorting package ' .$operation .' transaction list by requirements');

                Package_Dependency_Graph::determineInstallOrder();

                usort($identifiers, array('Package_Dependency_Graph', 'sortInstall'));
            }

            Package_Operation::dispatch($operation, $identifiers);
        }
    }
}