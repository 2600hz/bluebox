<?php defined('SYSPATH') or die('No direct access allowed.');

class sipinterfaces
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
            'null_option' => FALSE,
            'default_first' => TRUE,
            'unauth_before_auth' => FALSE
        );

        $data = arr::merge($defaults, $data);

        $options = array();

        $sipinterfaces = Doctrine::getTable('SipInterface')->findAll();

        foreach ($sipinterfaces as $sipinterface)
        {
            if (!$id = arr::get($sipinterface, 'sipinterface_id')
                    OR !$name = arr::get($sipinterface, 'name'))
            {
                continue;
            }

            if (($data['unauth_before_auth'] AND !$sipinterface['auth'])
                    OR (!$data['unauth_before_auth'] AND $sipinterface['auth']))
            {
               arr::unshift_assoc($options, $id, $name);

               continue;
            }

            $options[$id] = $name;
        }

        if ($data['default_first'] AND $default_sipinterface = SipInterface::get_default())
        {
            unset($options[$default_sipinterface['sipinterface_id']]);

            arr::unshift_assoc($options, $default_sipinterface['sipinterface_id'], $default_sipinterface['name']);
        }

        if ($data['null_option'])
        {
            arr::unshift_assoc($options, 0, $data['null_option']);
        }

        $data = array_diff($data, $defaults);

        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
}