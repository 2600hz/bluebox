<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/NetLists
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class netlists
{
    public static function dropdown($data, $selected = NULL, $extra = '', $nullOption = 'None')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => 'None'
        );

        arr::update($data, 'class', ' netlist_dropdown');

        // see if the module wants to allow null selections
        if (!empty($data['nullOption']))
        {
            $options = array('0' => __($data['nullOption']));
        } 
        else
        {
            $options = array();
        }
        
        unset($data['nullOption']);

        // build an array of netlists sutable for the dropdown helper
        $netLists = Doctrine::getTable('NetList')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($netLists as $netList)
        {
            $options[$netList['net_list_id']] = $netList['name'];
        }

        return form::dropdown($data, $options, $selected, $extra);
    }

    public static function getListName($id = NULL)
    {
        if (empty($id))
        {
            return FALSE;
        }
        
        $netlist = Doctrine::getTable('NetList')->findOneByNetListId($id);

        if (!empty($netlist['system_list']))
        {
            return $netlist['system_list'];
        } 
        else if (!empty($netlist['net_list_id']))
        {
            return 'net_list_' .$netlist['net_list_id'];
        }
        
        return FALSE;
    }

    public static function getSystemListId($name = NULL)
    {
        if (empty($name)) 
        {
            return NULL;
        }
        
        $netlist = Doctrine::getTable('NetList')->findOneBySystemList($name);
        
        if (!empty($netlist['net_list_id']))
        {
            return $netlist['net_list_id'];
        }
        
        return NULL;
    }

    public static function addToTrunkAuto($trunk)
    {
        $ip = $trunk->server;
        
        if (empty($ip))
        {
            return FALSE;
        }
        
        $netlist = Doctrine::getTable('NetList')->findOneBySystemList('trunks.auto');

        if (!$netlist)
        {
            $netlist = new NetList;

            $netlist['name'] = 'Trunks (auto)';

            $netList['system_list'] = 'trunks.auto';

            $netlist['allow'] = FALSE;
        }

        // check if the trunk server is a domain
        if ($ip != gethostbyname($ip))
        {
            // This is intended to detect all the addresses in a round-robin or
            // other multi-IP resolving DNS
            $netListItems = array();
            
            while(1)
            {
                for($i = 0; $i < 20; $i++)
                {
                    $resolvedIp = gethostbyname($ip) . '/32';

                    if (!in_array($resolvedIp, $netListItems))
                    {
                        $netListItems[] = $resolvedIp;
                        
                        break;
                    }
                }
                
                // we gave the dns 20 chances to update the ip, if it hasnt
                // yet or we are over 5 total then move on
                if ($i >= 20 || count($netListItems) > 5)
                {
                    break;
                }
            }
        } 
        else
        {
            // if the trunk server is an ip then thats all we need
            $netListItems = array($ip . '/32');
        }

        // get and delete any net list items already setup for this trunk
        $currentTrunkItems = Doctrine::getTable('NetListItem')->findByDescription('Trunk '. $trunk->trunk_id);

        if (!empty($currentTrunkItems))
        {
            foreach ($currentTrunkItems as $currentTrunkItem)
            {
                $currentTrunkItem->delete();
            }
        }

        // if there where no netListItems discovered above then we are as
        // up to date as we will ever be....
        if (empty($netListItems))
        {
            return FALSE;
        }

        // for each of the ip addresses we found make a new record
        foreach ($netListItems as $netListItem)
        {
            $item = new NetListItem;

            $item['record'] = $netListItem;

            $item['allow'] = TRUE;

            $item['description'] = 'Trunk '. $trunk['trunk_id'];

            $item['trunk_id'] = $trunk['trunk_id'];

            $netlist->NetListItem[] = $item;
        }

        // mark the parent dirty so we re-generate the acl xml
        $netlist->markModified('name');
        
        return $netlist->save();
    }

    public static function removeTrunkFromAuto($trunk)
    {
        // get and delete any net list items already setup for this trunk
        $currentTrunkItems = Doctrine::getTable('NetListItem')->findByDescription('Trunk '. $trunk->trunk_id);

        if (empty($currentTrunkItems))
        {
            return TRUE;
        }

        foreach ($currentTrunkItems as $currentTrunkItem)
        {
            $currentTrunkItem->delete();
        }

        $netlist = Doctrine::getTable('NetList')->findOneByName('Trunks (auto)');

        if (!$netlist)
        {
            return TRUE;
        }

        // mark the parent dirty so we re-generate the acl xml
        $netlist->markModified('name');
        
        return $netlist->save();
    }
}