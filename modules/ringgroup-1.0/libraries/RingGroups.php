<?php defined('SYSPATH') or die('No direct access allowed.');

class RingGroups
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $ringgroups = Doctrine::getTable('RingGroup')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($ringgroups as $ringgroup)
        {
            $target_objects[] = array(
                'object_name' => $ringgroup['name'],
                'object_description' => 'Ring Group',
                'object_number_type' => 'RingGroupNumber',
                'object_id' =>  $ringgroup['ring_group_id'],
            );
        }

        Event::$data['RingGroupNumber'] = array(
            'short_name' => 'ringgroup',
            'display_name' => 'Ring Group',
            'target_objects' => $target_objects,
            'quick_add' => '/ringgroup/create'
        );
    }
}