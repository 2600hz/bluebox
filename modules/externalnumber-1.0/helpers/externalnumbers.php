<?php defined('SYSPATH') or die('No direct access allowed.');

class externalnumbers
{
    public static function accountsDropdown($data, $selected = NULL, $extra = '')
    {
        $options = array('Select');

        // standardize the $data as an array, strings default to the class_type
        if ( !is_array($data) )
        {
            $data = array('name' => $data);
        }

        $selected = form::getRepopulateValue($data['name'], $selected);

        $records = Doctrine::getTable('Account')->findAll();

        foreach ($records as $record)
        {
            $record_id = $record['account_id'];

            $key = $record_id .'" title="account_' .$record_id .'"';

            $value = sprintf('%s (Account %s)', $record['name'], $record_id);

            $options[$key] = $value;

            if ($record_id == $selected)
            {
                $selected = $key;
            }
        }

        return form::dropdown($data, $options, $selected, $extra);
    }

    public static function locationsDropdown($data, $selected = NULL, $extra = '')
    {
        $options = array();

        if ( !is_array($data) )
        {
            $data = array('name' => $data);
        }

        $selected = form::getRepopulateValue($data['name'], $selected);

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        $records = Doctrine::getTable('Location')->findAll();

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        foreach ($records as $record)
        {
            $record_id = $record['location_id'];

            $key = $record_id .'" class="account_' .$record['account_id'] .'"';

            $options[$key] = $record['name'];

            if ($record_id == $selected)
            {
                $selected = $key;
            }
        }

        return form::dropdown($data, $options, $selected, $extra);
    }

    public static function contextsDropdown($data, $selected = NULL, $extra = '')
    {
        $options = array();

        if ( !is_array($data) )
        {
            $data = array('name' => $data);
        }

        $selected = form::getRepopulateValue($data['name'], $selected);

        Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        $records = Doctrine::getTable('Context')->findAll();

        Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        foreach ($records as $record)
        {
            $record_id = $record['context_id'];

            $key = $record_id .'" class="account_' .$record['account_id'] .'"';

            $options[$key] = $record['name'];

            if ($record_id == $selected)
            {
                $selected = $key;
            }
        }

        return form::dropdown($data, $options, $selected, $extra);
    }
}