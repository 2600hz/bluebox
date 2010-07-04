<?php defined('SYSPATH') or die('No direct access allowed.');

class Conferences
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $conferences = Doctrine::getTable('Conference')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($conferences as $conference)
        {
            $target_objects[] = array(
                'object_name' => $conference['name'],
                'object_description' => 'Conference Bridge',
                'object_number_type' => 'ConferenceNumber',
                'object_id' =>  $conference['conference_id'],
            );
        }

        Event::$data['ConferenceNumber'] = array(
            'short_name' => 'conference',
            'display_name' => 'Conference',
            'target_objects' => $target_objects,
            'quick_add' => '/conference/create'
        );
    }
}