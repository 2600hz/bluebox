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

        users::masqueradeAccount($account_id);


        // TODO: THIS NEEDS TO BE MOVED to the Feature Code module once we can ensure these items happen in order!

        $featureCode = new FeatureCode();
        
        $featureCode['name'] = 'Check voicemail';
        $featureCode['registry'] = array('feature' => 'voicemail_quickauth');
        //$featureCode['account_id'] = $account_id;
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
                return;
            }

            $number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $numberType['number_type_id'])
            ));

            //$number['account_id'] = $account_id;

            $number->save();
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize device number: ' .$e->getMessage());

            throw $e;
        }



        $ivrFeatureCode = new FeatureCode();

        $ivrFeatureCode['name'] = 'Return from IVR';
        $ivrFeatureCode['registry'] = array('feature' => 'ivr_return');
        //$ivrFeatureCode['account_id'] = $account_id;
        $ivrFeatureCode->save();

        try
        {
            $ivrNumber = new Number();

            $ivrNumber['user_id'] = $user_id;

            $ivrNumber['number'] = '2097';

            $ivrNumber['location_id'] = $location_id;

            $ivrNumber['registry'] = array(
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

            $ivrNumber['dialplan'] = $dialplan;

            $ivrNumber['class_type'] = 'FeatureCodeNumber';

            $ivrNumber['foreign_id'] = $ivrFeatureCode['feature_code_id'];

            $context = Doctrine::getTable('Context')->findOneByName('Outbound Routes');

            $ivrNumber['NumberContext']->fromArray(array(
                0 => array('context_id' => $context['context_id'])
            ));

            $numberType = Doctrine::getTable('NumberType')->findOneByClass('FeatureCodeNumber');

            if (empty($numberType['number_type_id']))
            {
                return;
            }

            $ivrNumber['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $numberType['number_type_id'])
            ));

            //$ivrNumber['account_id'] = $account_id;

            $ivrNumber->save();
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize device number: ' .$e->getMessage());

            throw $e;
        }





        // TODO: THIS NEEDS TO BE MOVED to the Voicemail module once we can ensure these items happen in order!

        $voicemail = new Voicemail();

        $voicemail['name'] = 'General voicemail box';
        $voicemail['mailbox'] = '2098';
        $voicemail['password'] = '2098';
        $voicemail->save();

        try
        {
            $vm_number = new Number();

            $vm_number['user_id'] = $user_id;

            $vm_number['number'] = '2098';

            $vm_number['location_id'] = $location_id;

            $vm_number['registry'] = array(
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

            $vm_number['dialplan'] = $dialplan;

            $vm_number['class_type'] = 'VoicemailNumber';

            $vm_number['foreign_id'] = $voicemail['voicemail_id'];

            $context = Doctrine::getTable('Context')->findOneByName('Outbound Routes');

            $vm_number['NumberContext']->fromArray(array(
                0 => array('context_id' => $context['context_id'])
            ));

            $vm_numberType = Doctrine::getTable('NumberType')->findOneByClass('VoicemailNumber');

            if (empty($vm_numberType['number_type_id']))
            {
                return;
            }

            $vm_number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $vm_numberType['number_type_id'])
            ));

            //$vm_number['account_id'] = $account_id;

            $vm_number->save();
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize device number: ' .$e->getMessage());

            throw $e;
        }


        $autoAttendant = new AutoAttendant();

        $autoAttendant['name'] = 'Main Auto-Attendant';
        $autoAttendant['description'] = 'Main Company Auto-Attendant';
        $autoAttendant['timeout'] = 5;
        $autoAttendant['digit_timeout'] = 3;
        $context = Doctrine::getTable('Context')->findOneByName('Inbound Routes');
        $autoAttendant['extension_context_id'] = $context['context_id'];

        $autoAttendant['extension_digits'] = (integer)4;
        //$autoAttendant['account_id'] = $account_id;

        $autoAttendant['keys'] = array(array('digits' => '*', 'number_id' => $number['number_id']),
                                       array('digits' => '9', 'number_id' => $vm_number['number_id']),
                                       array('digits' => '0', 'number_id' => $ivrNumber['number_id']));

        $autoAttendant['plugins'] = array('media' => array('type' => 'text_to_speech', 'tts_voice' => 'Flite/kal', 'tts_text' => 'Thank you for calling. Please dial the extension you wish to reach.'));

        $autoAttendant->save();

        Event::run('bluebox.autoattendant.initialize', $autoAttendant);        // Let anyone who cares initialize things related to auto-attendants

        users::restoreAccount();

    }
}