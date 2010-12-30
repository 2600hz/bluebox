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

    public static function getPackageMessages($messages, $displayedIdentifier)
    {
        $html = '';

        if(!$package = Package_Catalog::getPackageByIdentifier($displayedIdentifier))
        {
            return '';
        }

        $avaliable = Package_Catalog::getAvaliableVersions($package['packageName']);

        $avaliable[$displayedIdentifier] = $package['version'];

        foreach($messages as $type => $messageList)
        {
            foreach ($avaliable as $identifier => $version)
            {
                if (empty($messageList[$identifier]))
                {
                    continue;
                }

                $html .= '<div id="' .strtolower($package['packageName'] .'_' .$type) .'"';

                $html .= ' class="';

                $html .= ' ' .$type .'_message';

                $html .= ' ' .$package['packageName'] .'_message packagemanager index module">';

                $html .= __(ucfirst($type));

                $html .= '<ul class="' .$type .'_list packagemanager index module">';

                foreach($messageList[$identifier] as $message)
                {
                    $html .= '<li>' .__($message) .'</li>';
                }

                $html .= '</ul>';

                $html .= '</div>';
            }
        }
            
        return $html;
    }
}