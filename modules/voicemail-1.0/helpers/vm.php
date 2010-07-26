<?php defined('SYSPATH') or die('No direct access allowed.');

class vm
{
    public static function dropdown($data, $selected = NULL, $extra = '', $nullOption = 'None')
    {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => 'None'
        );

        arr::update($data, 'class', ' voicemail_dropdown');

        // see if the module wants to allow null selections
        if (!empty($data['nullOption']))
        {
            $options = array('0' => __($data['nullOption']));
        }
        else
        {
            $options = array();
        }

        unset($data['nullOption']);

        $voicemails = Doctrine::getTable('Voicemail')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($voicemails as $voicemail)
        {
            $options[$voicemail['voicemail_id']] = $voicemail['name'];
        }

        return form::dropdown($data, $options, $selected, $extra);
    }
}