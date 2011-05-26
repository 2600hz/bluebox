<?php defined('SYSPATH') or die('No direct access allowed.');

class PagingManager
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $groups = Doctrine::getTable('PagingGroup')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($groups as $group)
        {
            $target_objects[] = array(
                'object_name' => $group['pgg_name'],
                'object_description' => 'Paging Group',
                'object_number_type' => 'PagingGroupNumber',
                'object_id' =>  $group['pgg_id'],
            );
        }

        Event::$data['PagingGroupNumber'] = array(
            'short_name' => 'paginggroup',
            'display_name' => 'Paging Group',
            'target_objects' => $target_objects,
            'quick_add' => '/paging/create'
        );
    }
}