<?php defined('SYSPATH') or die('No direct access allowed.');

class packagemanager
{
    public static function avaliableActions($identifier)
    {
        $actions = html::anchor('packagemanager/verify/' .$identifier, __('Verify'), array('class' => 'packageAction'));

        $package = Package_Catalog::getPackageByIdentifier($identifier);

        switch ($package['status'])
        {
            case Package_Manager::STATUS_INSTALLED:
                $actions .= html::anchor('packagemanager/uninstall/' .$identifier, __('Uninstall'), array('class' => 'packageAction requiresConfirmation'));

                $actions .= html::anchor('packagemanager/repair/' .$identifier, __('Repair'), array('class' => 'packageAction'));

                if (!empty($package['upgrades']))
                {
                    $upgradePackage = reset($package['upgrades']);

                    $upgradeVersion = key($package['upgrades']);

                    $actions .= html::anchor('packagemanager/migrate/' .$upgradePackage, __('Update to ') .$upgradeVersion, array('class' => 'packageAction'));
                }
                
                break;

            case Package_Manager::STATUS_UNINSTALLED:
                $actions .= html::anchor('packagemanager/install/' .$identifier, __('Install'), array('class' => 'packageAction'));

                break;
        }

        return $actions;
    }
}