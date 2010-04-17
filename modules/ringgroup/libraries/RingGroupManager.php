<?php
class RingGroupManager
{
    public static function assignGroupMembers($ring_group_id, $devices)
    {
        $q = Doctrine_Query::create()->delete('RingGroupMember rgm')->where('rgm.ring_group_id = ?', $ring_group_id);
        $result = $q->execute();
        if (sizeof($devices) != 0) {
            foreach($devices as $device) {
                $RingGroupMember = new RingGroupMember();
                $RingGroupMember->ring_group_id = $ring_group_id;
                $RingGroupMember->device_id = $device;
                $RingGroupMember->delay_ring = 0;
                $RingGroupMember->save();
            }
        }
    }
    public static function getAssignedDevices($ring_group_id = NULL)
    {
        $devices = array();
        if (is_null($ring_group_id)) {
            return array();
        }
        $q = Doctrine_Query::create()->select('rgm.device_id')->from('RingGroupMember rgm')->where('rgm.ring_group_id = ?', array(
            $ring_group_id
        ));
        $result = $q->execute(array() , Doctrine::HYDRATE_ARRAY);
        foreach($result as $device) {
            $devices[] = (int)$device['device_id'];
        }
        return $devices;
    }
    public static function memberSelection($ring_group_id = NULL)
    {
        $devices = Doctrine::getTable('Device')->findAll();
        $assignedDevices = self::getAssignedDevices($ring_group_id);

        $options = array();
        $selected = array();
        foreach($devices as $device) {
            $number_id = $device->Number->number_id;
            $device_id = $device->device_id;
            $name = $device->name;
            $number = $device->Number->number;
            if (in_array($device_id, $assignedDevices)) {
                $selected[] = $device_id;
            } 
            $options[$device_id] = $number . ' (' . $name . ')';
        }

        if (!empty($_POST['_members']))
            $selected += $_POST['_members'];

        return form::dropdown(
            array(
                'id' => '_members',
                'name' => '_members[]',
                'class' => 'multiselect',
                'multiple' => 'multiple'
            ),
            $options,
            $selected
        );
    }
}
