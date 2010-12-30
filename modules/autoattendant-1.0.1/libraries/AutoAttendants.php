<?php defined('SYSPATH') or die('No direct access allowed.');

class AutoAttendants
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $autoattendants = Doctrine::getTable('AutoAttendant')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($autoattendants as $autoattendant)
        {
            $target_objects[] = array(
                'object_name' => $autoattendant['name'],
                'object_description' => 'Auto Attendant',
                'object_number_type' => 'AutoAttendantNumber',
                'object_id' =>  $autoattendant['auto_attendant_id'],
            );
        }

        Event::$data[] = array(
            'short_name' => 'autoattendant',
            'display_name' => 'Auto Attendant',
            'target_objects' => $target_objects,
            'quick_add' => '/autoattendant/create'
        );
    }
}