<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemails
{
    public static function provideNumberOptions()
    {
        Event::$data['VoicemailNumber'] = 'voicemail/numberOptions.mus';
    }

    public static function provideNumberTargets()
    {
        $target_objects = array();

        $voicemails = Doctrine::getTable('Voicemail')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($voicemails as $voicemail)
        {
            $target_objects[] = array(
                'object_name' => $voicemail['name'],
                'object_description' => 'Voicemail Box',
                'object_number_type' => 'VoicemailNumber',
                'object_id' =>  $voicemail['voicemail_id'],
            );
        }

        Event::$data['VoicemailNumber'] = array(
            'short_name' => 'voicemail',
            'display_name' => 'Voicemail Box',
            'target_objects' => $target_objects,
            'quick_add' =>'/voicemail/create'
        );
    }

    public static function provideNumberTerminators()
    {
        $target_objects = array();

        $voicemails = Doctrine::getTable('Voicemail')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($voicemails as $voicemail)
        {
            $target_objects[$voicemail['voicemail_id']] = $voicemail['name'];
        }

        return $target_objects;
    }
}