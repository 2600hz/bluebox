<?php defined('SYSPATH') or die('No direct access allowed.');

class packagemanager
{

    public static function dropdown($data, $package, $selected = NULL, $extra = '')
    {

        $options = array();

        switch($package['packageStatus']) {

            case Bluebox_PackageManager::STATUS_UNACCESSIBLE:

                break;

            case Bluebox_PackageManager::STATUS_UNINSTALLED:

                $options = array(
                    Bluebox_PackageManager::OPERATION_INSTALL => 'Install on Update',
                );

                break;

            case Bluebox_PackageManager::STATUS_DISABLED:

                $options = array(
                    Bluebox_PackageManager::OPERATION_ENABLE => 'Enable on Update',
                    Bluebox_PackageManager::OPERATION_UNINSTALL => 'Uninstall on Update',
                    Bluebox_PackageManager::OPERATION_REPAIR => 'Repair on Update'
                );

                break;

            case Bluebox_PackageManager::STATUS_INSTALLED:

                $options = array(
                    Bluebox_PackageManager::OPERATION_DISABLE => 'Disable on Update',
                    Bluebox_PackageManager::OPERATION_UNINSTALL => 'Uninstall on Update',
                    Bluebox_PackageManager::OPERATION_REPAIR => 'Repair on Update'
                );

                break;

        }

        $nullOption = ucfirst(Bluebox_PackageManager::statusToString($package['packageStatus']));

        array_unshift($options, $nullOption);

        return form::dropdown($data, $options, $selected, $extra);
        
    }
}