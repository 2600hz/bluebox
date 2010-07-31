<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Voicemail_Driver extends FreeSwitch_Base_Driver
{
    public static function set($voicemail)
    {
        $xml = FreeSwitch::setSection('user', 'voicemail_' .$voicemail['account_id'], $voicemail['voicemail_id']);

        $xml->update('/params/param[@name="sip-forbid-register"]{@value="true"}');

        $xml->update('/params/param[@name="vm-disk-quota"]{@value="1800"}');

        $xml->update('/params/param[@name="vm-password"]{@value="' . $voicemail['password'] . '"}');

        $xml->update('/params/param[@name="vm-message-ext "]{@value="' . $voicemail['audio_format'] . '"}');

        $xml->setAttributeValue('', 'id', $voicemail['mailbox']);

        if (!empty($voicemail['registry']['email_address']))
        {
            $xml->update('/params/param[@name="vm-mailto"]{@value="' .$voicemail['registry']['email_address'] .'"}');

            $xml->update('/params/param[@name="email-addr"]{@value="' .$voicemail['registry']['email_address'] .'"}');
        }

        //vm-keep-local-after-email
        if (empty($voicemail['registry']['delete_file']))
        {
            $xml->update('/params/param[@name="vm-delete-file"]{@value="false"}');
        }
        else
        {
            $xml->update('/params/param[@name="vm-delete-file"]{@value="true"}');
        }

        if (empty($voicemail['registry']['attach_audio_file']))
        {
            $xml->update('/params/param[@name="vm-attach-file"]{@value="false"}');
        }
        else
        {
            $xml->update('/params/param[@name="vm-attach-file"]{@value="true"}');
        }

        if (empty($voicemail['registry']['email_all_messages']))
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

    public static function delete($voicemail)
    {
        $xml = FreeSwitch::setSection('user', 'voicemail_' .$voicemail['account_id'], $voicemail['voicemail_id']);

        $xml->deleteNode();
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
        
        $xml->update('/action[@application="voicemail"]{@data="default ' . $domain . ' ' . $destination['mailbox'] .'"}');

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

        $xml->update('/action[@application="voicemail"]{@data="default ' . $domain .' ' .$voicemail['mailbox'] .'"}');

        $xml->update('/action[@application="hangup"]');
    }
}