<?php defined('SYSPATH') or die('No direct access allowed.');

class usermanager {

    public static function getUserTypes()
    {
        return array(
            User::TYPE_GUEST => 'Guest',
            User::TYPE_RESTRICTED_USER => 'Restricted User',
            User::TYPE_NORMAL_USER => 'User',
            User::TYPE_POWER_USER => 'Power User',
            User::TYPE_RESTRICTED_ADMIN => 'Restricted Admin',
            User::TYPE_ACCOUNT_ADMIN => 'Account Admin',
            User::TYPE_SYSTEM_ADMIN => 'God'
        );
    }

    public static function dropdownUserType($data, $selected = NULL, $extra = '')
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
        $data = arr::update($data, 'class', 'user_type_dropdown');

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

        $userTypes = self::getUserTypes();

        foreach($userTypes as $userType => $displayName)
        {
            if ($userType <= users::$user['user_type'])
            {
                $options[$userType] = $displayName;
            }
        }
        
        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
}
