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
        $this->transaction[$identifier] = Package_Manager::OPERATION_INSTALL;
    }

    public function uninstall($identifier)
    {
        $this->transaction[$identifier] = Package_Manager::OPERATION_UNINSTALL;
    }

    public function verify($identifier)
    {
        $this->transaction[$identifier] = Package_Manager::OPERATION_VERIFY;
    }

    public function repair($identifier)
    {
        $this->transaction[$identifier] = Package_Manager::OPERATION_REPAIR;
    }

    public function migrate($identifier)
    {
        $this->transaction[$identifier] = Package_Manager::OPERATION_MIGRATE;
    }

    public function commit()
    {
        foreach ($this->transaction as $identifier => $operation)
        {
            Package_Operation::dispatch($operation, $identifier);
        }
    }

}