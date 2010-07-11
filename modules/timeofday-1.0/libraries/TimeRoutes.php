<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeRoutes
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $routes = Doctrine::getTable('TimeOfDay')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($routes as $route)
        {
            $target_objects[] = array(
                'object_name' => $route['name'],
                'object_description' => 'Time of Day Rule',
                'object_number_type' => 'TimeOfDayNumber',
                'object_id' =>  $route['time_of_day_id']
            );
        }

        Event::$data['TimeOfDayNumber'] = array(
            'short_name' => 'timeofday',
            'display_name' => 'Time of Day',
            'target_objects' => $target_objects,
            'quick_add' => '/timeofday/create'
        );
    }
}