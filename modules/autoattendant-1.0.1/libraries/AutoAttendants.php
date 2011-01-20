<?php defined('SYSPATH') or die('No direct access allowed.');

class AutoAttendants
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $autoattendants = Doctrine::getTable('AutoAttendant')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($autoattendants as $autoattendant)
        {
            $target_objects[] = array(
                'object_name' => $autoattendant['name'],
                'object_description' => 'Auto Attendant',
                'object_number_type' => 'AutoAttendantNumber',
                'object_id' =>  $autoattendant['auto_attendant_id'],
            );
        }

        Event::$data[] = array(
            'short_name' => 'autoattendant',
            'display_name' => 'Auto Attendant',
            'target_objects' => $target_objects,
            'quick_add' => '/autoattendant/create'
        );
    }

    public static function initSampleData()
    {
        $account_id = Event::$data['account_id'];
        $location_id = Event::$data['Location'][0]['location_id'];
        $user_id = Event::$data['Location'][0]['User'][0]['user_id'];

        $keys = array();

        // TODO: THIS NEEDS TO BE MOVED to the Feature Code module once we can ensure these items happen in order!
        if (($number_id = self::voicemailQuickAuth($account_id, $location_id, $user_id)))
        {
            $keys[] = array('digits' => '*', 'number_id' => $number_id);
        }

        if(($number_id = self::ivrReturn($account_id, $location_id, $user_id)))
        {
            $keys[] = array('digits' => '0', 'number_id' => $number_id);
        }

        // TODO: THIS NEEDS TO BE MOVED to the Voicemail module once we can ensure these items happen in order!
        if(($number_id = self::genericVoicemail($account_id, $location_id, $user_id)))
        {
            $keys[] = array('digits' => '9', 'number_id' => $number_id);
        }

        $autoAttendant = new AutoAttendant();

        $autoAttendant['name'] = 'Main Auto-Attendant';
        $autoAttendant['description'] = 'Main Company Auto-Attendant';
        $autoAttendant['timeout'] = 5;
        $autoAttendant['digit_timeout'] = 3;
        $context = Doctrine::getTable('Context')->findOneByName('Inbound Routes');
        $autoAttendant['extension_context_id'] = $context['context_id'];

        $autoAttendant['extension_digits'] = (integer)4;

        $autoAttendant['keys'] = $keys;

        $autoAttendant['plugins'] = array('media' => array('type' => 'text_to_speech', 'tts_voice' => 'Flite/kal', 'tts_text' => 'Thank you for calling. Please dial the extension you wish to reach.'));

        $autoAttendant->save();

        // Let anyone who cares initialize things related to auto-attendants
        Event::run('bluebox.autoattendant.initialize', $autoAttendant);        
    }

    public static function genericVoicemail($account_id, $location_id, $user_id)
    {
        if (!class_exists('Voicemail'))
        {
            return FALSE;
        }
        
        $voicemail = new Voicemail();

        $voicemail['name'] = 'General voicemail box';

        $voicemail['mailbox'] = '2098';

        $voicemail['password'] = '2098';

        $voicemail->save();

        try
        {
            $number = new Number();

            $number['user_id'] = $user_id;

            $number['number'] = '2098';

            $number['location_id'] = $location_id;

            $number['registry'] = array(
                'ignoreFWD' => '0',
                'ringtype' => 'ringing',
                'timeout' => 20
            );

            $dialplan = array(
                'terminate' => array(
                    'transfer' => 0,
                    'voicemail' => 0,
                    'action' => 'hangup'
                )
            );

            $number['dialplan'] = $dialplan;

            $number['class_type'] = 'VoicemailNumber';

            $number['foreign_id'] = $voicemail['voicemail_id'];

            $context = Doctrine::getTable('Context')->findOneByName('Outbound Routes');

            $number['NumberContext']->fromArray(array(
                0 => array('context_id' => $context['context_id'])
            ));

            $vm_numberType = Doctrine::getTable('NumberType')->findOneByClass('VoicemailNumber');

            if (empty($vm_numberType['number_type_id']))
            {
                return FALSE;
            }

            $number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $vm_numberType['number_type_id'])
            ));

            $number->save();

            return $number['number_id'];
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize voicemail for auto-attendant: ' .$e->getMessage());

            throw $e;
        }
    }

    public static function voicemailQuickAuth($account_id, $location_id, $user_id)
    {
        if (!class_exists('FeatureCode'))
        {
            return FALSE;
        }
        
        $featureCode = new FeatureCode();

        $featureCode['name'] = 'Check voicemail';

        $featureCode['registry'] = array('feature' => 'voicemail_quickauth');

        $featureCode->save();

        try
        {
            $number = new Number();

            $number['user_id'] = $user_id;

            $number['number'] = '2099';

            $number['location_id'] = $location_id;

            $number['registry'] = array(
                'ignoreFWD' => '0',
                'ringtype' => 'ringing',
                'timeout' => 20
            );

            $dialplan = array(
                'terminate' => array(
                    'transfer' => 0,
                    'voicemail' => 0,
                    'action' => 'hangup'
                )
            );

            $number['dialplan'] = $dialplan;

            $number['class_type'] = 'FeatureCodeNumber';

            $number['foreign_id'] = $featureCode['feature_code_id'];

            $context = Doctrine::getTable('Context')->findOneByName('Outbound Routes');

            $number['NumberContext']->fromArray(array(
                0 => array('context_id' => $context['context_id'])
            ));

            $numberType = Doctrine::getTable('NumberType')->findOneByClass('FeatureCodeNumber');

            if (empty($numberType['number_type_id']))
            {
                return FALSE;
            }

            $number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $numberType['number_type_id'])
            ));

            $number->save();

            return $number['number_id'];
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize check voicemail for auto-attendants: ' .$e->getMessage());

            throw $e;
        }
    }

    public static function ivrReturn($account_id, $location_id, $user_id)
    {
        if (!class_exists('FeatureCode'))
        {
            return FALSE;
        }
        
        $featureCode = new FeatureCode();

        $featureCode['name'] = 'Return from IVR';

        $featureCode['registry'] = array('feature' => 'ivr_return');

        $featureCode->save();

        try
        {
            $number = new Number();

            $number['user_id'] = $user_id;

            $number['number'] = '2097';

            $number['location_id'] = $location_id;

            $number['registry'] = array(
                'ignoreFWD' => '0',
                'ringtype' => 'ringing',
                'timeout' => 20
            );

            $dialplan = array(
                'terminate' => array(
                    'transfer' => 0,
                    'voicemail' => 0,
                    'action' => 'hangup'
                )
            );

            $number['dialplan'] = $dialplan;

            $number['class_type'] = 'FeatureCodeNumber';

            $number['foreign_id'] = $featureCode['feature_code_id'];

            $context = Doctrine::getTable('Context')->findOneByName('Outbound Routes');

            $number['NumberContext']->fromArray(array(
                0 => array('context_id' => $context['context_id'])
            ));

            $numberType = Doctrine::getTable('NumberType')->findOneByClass('FeatureCodeNumber');

            if (empty($numberType['number_type_id']))
            {
                return FALSE;
            }

            $number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $numberType['number_type_id'])
            ));

            $number->save();

            return $number['number_id'];
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize device number: ' .$e->getMessage());

            throw $e;
        }
    }
}