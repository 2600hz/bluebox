<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Catalog_Standardize extends Package_Catalog
{
    protected static function packageData(&$metadata, $filepath)
    {
        $metadata['directory'] = dirname($filepath);

        if (empty($metadata['packageName']))
        {
            $metadata['packageName'] = dirname(str_replace(DOCROOT, '', $filepath));
        }

        $metadata['identifier'] = md5($metadata['packageName'] .$metadata['version']);

        if (empty($metadata['displayName']))
        {
            $metadata['displayName'] = ucfirst(inflector::humanize($metadata['packageName']));
        }

        if (!is_bool($metadata['default']))
        {
            $metadata['default'] = FALSE;
        }

        if (!is_array($metadata['required']))
        {
            $metadata['required'] = array();

            kohana::log('error', 'Package ' . $metadata['packageName'] . ' required parameter is poorly formated, ignoring');
        }

        if(is_numeric($metadata['version']))
        {            
            $versionParts = explode('.', $metadata['version']);

            $versionParts = array_pad($versionParts, 3, 0);

            $metadata['version'] = implode('.', $versionParts);
        }

        $metadata['version'] = (string)$metadata['version'];
        
        $metadata['upgrades'] = array();

        $metadata['downgrades'] = array();

        $metadata['basedir'] = dirname(str_replace(DOCROOT, '', $filepath));

        $metadata['status'] = Package_Manager::STATUS_UNINSTALLED;

        $metadata['datastore_id'] = NULL;
    }

    protected static function navigation(&$metadata)
    {
        // if the navStructures array is missing or not an array build it from the individual values
        if (!isset($metadata['navStructures']) || !is_array($metadata['navStructures']))
        {
            if (!is_null($metadata['navURL']))
            {
                $metadata['navStructures'] = array(array_intersect_key(
                    $metadata,
                    array_flip(array('navBranch', 'navURL', 'navLabel', 'navSummary', 'navSubmenu'))
                ));
            }
            else if ($metadata['type'] == Package_Manager::TYPE_MODULE)
            {
                 kohana::log('error', 'Package ' . $metadata['packageName'] . ' of type module does not have any valid navigation defined');
            }
        }

        // if the navStructures is an array make sure it is in the correct format
        if (isset($metadata['navStructures']))
        {
            if (!array_key_exists(0, $metadata['navStructures']))
            {
                $metadata['navStructures'] = array($metadata['navStructures']);
            }

            foreach ($metadata['navStructures'] as $key => $navStructure)
            {
                // each navigation structure must have the base url defined
                if (empty($navStructure['navURL']))
                {
                    kohana::log('error', 'Package ' . $metadata['packageName'] . ' has defined invalid navigation, ignoring');

                    unset($metadata['navStructures'][$key]);

                    continue;
                }

                // if the navigation structure does not have a lable use the package display name
                if (empty($navStructure['navLabel']))
                {
                    $metadata['navStructures'][$key]['navLabel'] = $metadata['displayName'];
                }

                // if the navigation structure does not have a summary use the package summary
                if (empty($navStructure['navSummary']))
                {
                    $metadata['navStructures'][$key]['navSummary'] = $metadata['summary'];
                }

                // if the navigation structure doesn not define a branch then assume 'root'
                if (empty($navStructure['navBranch']))
                {
                    $metadata['navStructures'][$key]['navBranch'] = '/';
                }

                // if the navigation structure does not have a submenu or that submenu
                // is not an array then defualt to an empty array.  Otherwise check the submenu
                if (!isset($navStructure['navSubmenu']))
                {
                    $metadata['navStructures'][$key]['navSubmenu'] = array();
                }
                else if (!is_array($navStructure['navSubmenu']))
                {
                    kohana::log('error', 'Package ' . $metadata['packageName'] . ' defined an invalid submenu!');

                    $metadata['navStructures'][$key]['navSubmenu'] = array();
                }
                else
                {
                    $submenuItems = array();

                    foreach ($navStructure['navSubmenu'] as $name => $submenu)
                    {
                        if (is_string($submenu))
                        {
                            $submenu = array ('url' => $submenu);
                        }

                        if (empty($submenu['url']))
                        {
                            kohana::log('error', 'Package ' . $metadata['packageName'] . ' defined an invalid submenu item ' .$name);

                            continue;
                        }
                        else
                        {
                            $submenuItem = &$submenuItems[$name];

                            $submenuItem['url'] = $submenu['url'];
                        }

                        if (empty($submenu['disabled']))
                        {
                            $submenuItem['disabled'] = FALSE;
                        }
                        else
                        {
                            $submenuItem['disabled'] = TRUE;
                        }

                        if (trim($submenuItem['url'], '/') == trim($navStructure['navURL'], '/'))
                        {
                            $submenuItem['entry'] = TRUE;
                        }
                        else
                        {
                            $submenuItem['entry'] = FALSE;
                        }
                    }

                    $metadata['navStructures'][$key]['navSubmenu'] = $submenuItems;
                }
            }
        }
        else
        {
            $metadata['navStructures'] = array();
        }

        // remove the unecessray configure variables
        unset(
            $metadata['navBranch'],
            $metadata['navURL'],
            $metadata['navLabel'],
            $metadata['navSummary'],
            $metadata['navSubmenu']
        );
    }

    protected static function typeRestrictions(&$metadata)
    {
        switch($metadata['type'])
        {
            case Package_Manager::TYPE_DEFAULT:
                kohana::log('alert', 'Package ' . $metadata['packageName'] . ' is using the default package type');

                break;

            case Package_Manager::TYPE_CORE:
                $metadata['default'] = TRUE;

                $metadata['denyRemoval'] = TRUE;

                break;

            case Package_Manager::TYPE_MODULE:
            case Package_Manager::TYPE_PLUGIN:
            case Package_Manager::TYPE_DRIVER:
            case Package_Manager::TYPE_SERVICE:
            case Package_Manager::TYPE_DIALPLAN:
            case Package_Manager::TYPE_ENDPOINT:
            case Package_Manager::TYPE_SKIN:
                break;

            default:
                $metadata['type'] = Package_Manager::TYPE_DEFAULT;

                kohana::log('error', 'Package ' . $metadata['packageName'] . ' is using an invalid package type, set to default');
        }
    }
}
