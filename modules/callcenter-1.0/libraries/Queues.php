<?php defined('SYSPATH') or die('No direct access allowed.');

class Queues
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $queues = Doctrine::getTable('Queue')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($queues as $queue)
        {
            $target_objects[] = array(
                'object_name' => $queue['name'],
                'object_description' => 'Call Queue for Call Center',
                'object_number_type' => 'QueueNumber',
                'object_id' =>  $queue['queue_id'],
            );
        }

        Event::$data['QueueNumber'] = array(
            'short_name' => 'queue',
            'display_name' => 'Queue',
            'target_objects' => $target_objects
        );
    }
}
