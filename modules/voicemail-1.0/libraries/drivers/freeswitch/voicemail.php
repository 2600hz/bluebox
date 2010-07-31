<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Voicemail_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if ($base instanceof Voicemail)
        {
            $xml = FreeSwitch::setSection('user', 'voicemail_' .$base['account_id'], $base['voicemail_id']);

            $xml->update('/params/param[@name="sip-forbid-register"]{@value="true"}');

            $xml->update('/params/param[@name="vm-disk-quota"]{@value="1800"}');

            $xml->update('/params/param[@name="vm-password"]{@value="' . $base['password'] . '"}');

            $xml->update('/params/param[@name="vm-message-ext "]{@value="' . $base['audio_format'] . '"}');

            $xml->setAttributeValue('', 'id', $base['mailbox']);

            if (!empty($base['registry']['email_address']))
            {
                $xml->update('/params/param[@name="vm-mailto"]{@value="' .$base['registry']['email_address'] .'"}');

                $xml->update('/params/param[@name="email-addr"]{@value="' .$base['registry']['email_address'] .'"}');
            }

            //vm-keep-local-after-email
            if (empty($base['registry']['delete_file']))
            {
                $xml->update('/params/param[@name="vm-delete-file"]{@value="false"}');
            }
            else
            {
                $xml->update('/params/param[@name="vm-delete-file"]{@value="true"}');
            }

            if (empty($base['registry']['attach_audio_file']))
            {
                $xml->update('/params/param[@name="vm-attach-file"]{@value="false"}');
            }
            else
            {
                $xml->update('/params/param[@name="vm-attach-file"]{@value="true"}');
            }

            if (empty($base['registry']['email_all_messages']))
            {
                $xml->update('/params/param[@name="vm-email-all-messages"]{@value="false"}');
            }
            else
            {
                $xml->update('/params/param[@name="vm-email-all-messages"]{@value="true"}');
            }

            //vm-notify-email-all-messages

            //vm_cc

            //$xml->update($prefix . '/params/param[@name="storage-dir"]{@value="' . $voicemail->getStorageDir() . '"}');

            //$xml->update($prefix . '/params/param[@name="vm-alternate-greet-id"]{@value="' . $voicemail->getGreetings() . '"}');
        }
        else if($base instanceof Device)
        {

            $voicemail = $base['plugins']['voicemail'];

            $domain = '$${location_' .$base['User']['location_id'] .'}';

            $xml = FreeSwitch::setSection('user', $domain, $base['device_id']);



            if (empty($base['plugins']['voicemail']['mwi_box']))
            {
                 $xml->deleteNode('/params/param[@name="mwi-account"]');
            }
            else
            {
                $voicemail = Doctrine::getTable('Voicemail')->find($base['plugins']['voicemail']['mwi_box']);

                if (!$voicemail)
                {
                    $xml->deleteNode('/params/param[@name="mwi-account"]');
                }
                else
                {
                    $xml->update('/params/param[@name="mwi-account"]{@value="' .$voicemail['mailbox'] .'@voicemail_' . $base['account_id'] .'"}');
                }
            }
        }
    }

    public static function delete($base)
    {
        if ($base instanceof Voicemail)
        {
            $xml = FreeSwitch::setSection('user', 'voicemail_' .$base['account_id'], $base['voicemail_id']);

            $xml->deleteNode();
        }
        else if($base instanceof Device)
        {
            $voicemail = $base['plugins']['voicemail'];

            $domain = '$${location_' .$base['User']['location_id'] .'}';

            $xml = FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->deleteNode('/params/param[@name="mwi-account"]');
        }
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        $domain = 'voicemail_' .$destination['account_id'];

        //voicemail_greeting_number
        
        $xml->update('/action[@application="answer"]');

        $xml->update('/action[@application="sleep"]{@data="1000"}');

        if (!empty($number['registry']['skipGreeting']))
        {
            $xml->update('/action[@application="set"][@data="skip_greeting=true"]');
        }

        if (!empty($number['registry']['skipInstructions']))
        {
            $xml->update('/action[@application="set"][@data="skip_instructions=true"]');
        }
        
        $xml->update('/action[@application="voicemail"]{@data="default voicemail_' .$destination['account_id'] .' ' .$destination['mailbox'] .'"}');

        $xml->update('/action[@application="hangup"]');
    }

    public static function preNumber()
    {
        $xml = Telephony::getDriver()->xml;

        $number = Event::$data;

        $dialplan = $number['dialplan'];

        if (empty($dialplan['terminate']['action']))
        {
            return;
        }

        if ($dialplan['terminate']['action'] != 'voicemail')
        {
            return;
        }

        if (empty($dialplan['terminate']['voicemail']))
        {
            return;
        }

        $xml->update('/action[@application="set"][@bluebox="settingEndBridge"][@data="hangup_after_bridge=true"]');
        
        $xml->update('/action[@application="set"][@bluebox="settingFail"][@data="continue_on_fail=true"]');
    }

    public static function postnumber()
    {
        $xml = Telephony::getDriver()->xml;

        $number = Event::$data;

        $dialplan = $number['dialplan'];

        if (empty($dialplan['terminate']['action']))
        {
            return;
        }

        if ($dialplan['terminate']['action'] != 'voicemail')
        {
            return;
        }

        if (empty($dialplan['terminate']['voicemail']))
        {
            return;
        }

        $voicemail = Doctrine::getTable('Voicemail')->find($dialplan['terminate']['voicemail']);

        if (!$voicemail)
        {
            return;
        }

        $destination = $number['Destination'];

        $domain = 'voicemail_' .$destination['account_id'];

        $xml->update('/action[@application="voicemail"]{@data="default voicemail_' .$destination['account_id'] .' ' .$voicemail['mailbox'] .'"}');

        $xml->update('/action[@application="hangup"]');
    }
}