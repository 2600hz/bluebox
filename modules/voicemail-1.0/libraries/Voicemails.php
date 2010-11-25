<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemails
{
    public static function createExtension()
    {
        Event::$data += array(
            'voicemail_password' => self::generatePin(),
            'voicemail_timezone' => kohana::config('locale.timezone'),
            'voicemail_email_all_messages' => empty(Event::$data['user']['email_address']) ? 0 : 1,
            'voicemail_delete_file' => 0,
            'voicemail_attach_audio_file' => 1,
            'voicemail_email_address' => empty(Event::$data['user']['email_address']) ? '' : Event::$data['user']['email_address']
        );

        extract(Event::$data);

        Doctrine::getTable('Voicemail')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        $name_append = '';

        if (!empty($owner_name))
        {
            $name_append = ' for ' .$owner_name;
        }

        try
        {
            $voicemail = new Voicemail();

            $voicemail['name'] = 'VM ' .$extension .$name_append;

            $voicemail['mailbox'] = $extension;

            $voicemail['password'] = $voicemail_password;

            $voicemail['account_id'] = $account_id;

            $voicemail['plugins'] = array('timezone' => array(
                'timezone' => $voicemail_timezone
            ));

            $voicemail['registry'] = array(
                'email_all_messages' => $voicemail_email_all_messages,
                'delete_file' => $voicemail_delete_file,
                'attach_audio_file' => $voicemail_attach_audio_file,
                'email_address' => $voicemail_email_address
            );

            $voicemail->save();

            $plugin = array('voicemail' => array(
                'mwi_box' => $voicemail['voicemail_id']
            ));

            $device['plugins'] = arr::merge($device['plugins'], $plugin);
        }
        catch (Exception $e)
        {
            Doctrine::getTable('Voicemail')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
            
            kohana::log('error', 'Unable to generate voicemail for device: ' .$e->getMessage());

            throw $e;
        }

        Doctrine::getTable('Voicemail')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
    }

    public static function provideNumberTerminator()
    {
        Event::$data['VoicemailNumber'] = 'voicemail/terminateOption.mus';
    }
    
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

    public static function generatePin($length = 4)
    {
        $pin = '';

        $alt = time() % 2;

        for ($i = 0; $i < $length; $i++)
        {
            $pin .= rand(0,9);
        }

        return $pin;
    }
}