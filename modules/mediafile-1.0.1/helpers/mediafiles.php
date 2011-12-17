<?php defined('SYSPATH') or die('No direct access allowed.');

class mediafiles
{
    public static function dropdown($data, $selected = NULL, $extra = '')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( !is_array($data) )
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $defaults = array(
            'nullOption' => FALSE
        );

        $data = arr::merge($defaults, $data);

        $options = MediaFile::catalog();

        if ($data['nullOption'])
        {
            array_unshift($options, $data['nullOption']);
        }

        $data = array_diff($data, $defaults);

        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
}