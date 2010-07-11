<?php defined('SYSPATH') or die('No direct access allowed.');

class sip
{    
    public static function dropdownInviteFormat($data, $selected = NULL, $extra = '')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => FALSE
        );

        // append or insert the class
        $data = arr::update($data, 'class', ' sip_invite_format_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption']))
        {
            $options = array(0 => $data['nullOption']);
        } 
        else
        {
            $options = array();
        }
        
        unset($data['nullOption']);

        $options = Sip_Plugin::getInviteFormats();
        
        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }

    public static function dropdownCIDFormat($data, $selected = NULL, $extra = '')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => FALSE
        );

        // append or insert the class
        $data = arr::update($data, 'class', ' sip_invite_format_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption']))
        {
            $options = array(0 => $data['nullOption']);
        } 
        else
        {
            $options = array();
        }

        unset($data['nullOption']);

        $options = Sip_Plugin::getCIDFormats();

        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
}
