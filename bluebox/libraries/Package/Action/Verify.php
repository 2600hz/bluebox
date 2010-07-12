<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Action_Verify extends Package_Action
{
    public static function execute($args)
    {
        if (!is_array($args))
        {
            $args = array($args);
        }

        if (empty($args[0]))
        {
            throw new Package_Action_Exception('Verify requires the package identifier to verify');
        }

        $identifier = $args[0];

        kohana::log('debug', 'Starting verify of ' .$identifier);

        self::verify($identifier);

        Package_Dependency::validateIntegration($identifier);
    }

    protected static function verify($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->verify();
    }
}