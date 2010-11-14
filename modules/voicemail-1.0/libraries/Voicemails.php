<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemails
{
    public static function initializeDevice()
    {
        extract(Event::$data);

        Doctrine::getTable('Voicemail')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        try
        {
            $voicemail = new Voicemail();

            $voicemail['name'] = 'Voicemail for ' .$device['name'];

            $voicemail['mailbox'] = $extension;

            $voicemail['password'] = self::generatePin();

            $voicemail['account_id'] = $account_id;

            $voicemail['plugins'] = array('timezone' => array(
                'timezone' => kohana::config('locale.timezone')
            ));

            if (($user = Doctrine::getTable('User')->find($user_id)) AND !empty($user['email_address']))
            {
                $voicemail['registry'] = array(
                    'email_all_messages' => 1,
                    'delete_file' => 0,
                    'attach_audio_file' => 1,
                    'email_address' => $user['email_address']
                );
            }

            $voicemail->save();

            $plugin = array('voicemail' => array(
                'mwi_box' => $voicemail['voicemail_id']
            ));

            $device['plugins'] = arr::merge($device['plugins'], $plugin);
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to generate voicemail for device: ' .$e->getMessage());
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