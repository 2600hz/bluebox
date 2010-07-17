<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Operation_Verify extends Package_Operation
{
    public function validate($identifier)
    {
        
    }

    public function preExec($identifier)
    {

    }

    public function exec($identifier)
    {
        self::verify($identifier);

        self::runCheckMethods($identifier);

        Package_Dependency::validateIntegration($identifier);
    }

    public function postExec($identifier)
    {

    }

    public function finalize($identifier)
    {

    }

    protected static function verify($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $configureInstance->verify();
    }

    protected static function runCheckMethods($identifier)
    {
        $configureInstance = Package_Catalog::getPackageConfigureInstance($identifier);

        $checkMethods = get_class_methods(get_class($configureInstance));

        $checkMethods = array_filter($checkMethods, array(
            __CLASS__,
            '_filterCheckMethods'
        ));

        if (empty($checkMethods) || !is_array($checkMethods))
        {
            return;
        }
        
        foreach($checkMethods as $checkMethod)
        {
            try
            {
                kohana::log('debug', 'Running check method ' .get_class($configureInstance) .'::' .$checkMethod .'();');

                $return = call_user_func(array($configureInstance, $checkMethod));
            }
            catch (Exception $e)
            {
                //TODO: Still have not figure out how to handle errors...
            }
        }
    }

    private static function _filterCheckMethods($methodName)
    {
        return strstr($methodName, '_check');
    }
}