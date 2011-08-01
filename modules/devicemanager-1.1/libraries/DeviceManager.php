<?php defined('SYSPATH') or die('No direct access allowed.');

class DeviceManager
{
    public static function provideNumberOptions()
    {
        Event::$data['DeviceNumber'] = 'devicemanager/numberOptions.mus';
    }

    public static function provideNumberTargets()
    {
        $target_objects = array();

        $devices = Doctrine::getTable('Device')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($devices as $device)
        {
            $target_objects[] = array(
                'object_name' => $device['name'],
                'object_description' => str_replace('Device', ' Phone', $device['type']),
                'object_number_type' => 'DeviceNumber',
                'object_id' =>  $device['device_id'],
            );
        }

        Event::$data['DeviceNumber'] = array(
            'short_name' => 'device',
            'display_name' => 'Device',
            'target_objects' => $target_objects,
            'quick_add' => '/devicemanager/create'
        );
    }

    public static function getAvaliableRingGroupMembers()
    {
        $members = &Event::$data;

        $devices = Doctrine::getTable('Device')->findAll();

        foreach ($devices as $device)
        {
            if (!empty($device['plugins']['sip']))
            {
                switch(Telephony::getDriverName())
                {
                    case 'Asterisk':
                        $bridge  = 'SIP/';

                        $bridge .= $device['plugins']['sip']['username'];
                        
                        break;

                    case 'FreeSwitch':
                        $bridge  = 'user/';

                        $bridge .= $device['plugins']['sip']['username'];

                        $bridge .= '@$${location_' .$device['User']['location_id'] .'}';

                        break;

                    default:
                        continue 2;
                }

                $member = array(
                    'bridge' => $bridge,
                    'display_name' => $device['name'],
                    'display_type' => 'Sip Device',
                    'id' => $device['device_id'],
                    'type' => 'Device'
                );
            }
            else
            {
                continue;
            }

            $members[] = $member;
        }
    }
}