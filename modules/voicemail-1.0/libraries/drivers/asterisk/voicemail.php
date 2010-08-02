<?php defined('SYSPATH') or die('No direct access allowed.');

class Asterisk_Voicemail_Driver extends Asterisk_Base_Driver
{
    public static function set($base)
    {
        // Get the asterisk manager
        $doc = Telephony::getDriver()->doc;
        
        if ($base instanceof Voicemail)
        {
            // if this is an update of an existing user, remove the old entry
            $modified = $base->getModified(TRUE, TRUE);

            if (!empty($modified['mailbox']))
            {
                if ($modified['mailbox'] != $base['mailbox'])
                {
                    //TODO: do we have a way to do remove a 'section'?
                }
            }

            $mailbox['password'] = $base['password'];

            $mailbox['user_name'] = 'Voicemail';

            $mailbox['email_address'] = '';

            $mailbox['pager_email_address'] = '';

            $mailbox['options'] = array();

            // If the user has provided an email then also setup any additional settings
            if (!empty($base['registry']['email_all_messages']))
            {
                $mailbox['email_address'] = $base['registry']['email_address'];

                if (!empty($base['registry']['attach_audio_file']))
                {
                   $mailbox['options']['attach'] = 'attach=yes';
                }
                else
                {
                   $mailbox['options']['attach'] = 'attach=no';
                }

                if (!empty($base['registry']['delete_file']))
                {
                   $mailbox['options']['delete'] = 'delete=yes';
                }
                else
                {
                   $mailbox['options']['delete'] = 'delete=no';
                }
            }

            // The mailbox options need to be seperated by a pipe
            $mailbox['options'] = implode('|', $mailbox['options']);

            $mailbox = array_filter($mailbox);

            $location = 'voicemail_' .$base['account_id'];

            $doc->setPosition('voicemail.conf', $location);
            
            $doc->update('voicemail.conf', $location, $base['mailbox'], '>' .implode(',', $mailbox));
        }
        else if ($base instanceof Device)
        {
            $voicemail = $base['plugins']['voicemail'];

            if (empty($base['plugins']['sip']))
            {
                return;
            }

            $sip = $base['plugins']['sip'];

            // Get the username of this sip device
            $section = $sip['username'];

            $doc->setPosition('sip.conf', $section);

            if (empty($base['plugins']['voicemail']['mwi_box']))
            {
                 //$xml->deleteNode('/params/param[@name="mwi-account"]');
            }
            else
            {
                $voicemail = Doctrine::getTable('Voicemail')->find($base['plugins']['voicemail']['mwi_box']);

                if (!$voicemail)
                {
                    //$xml->deleteNode('/params/param[@name="mwi-account"]');
                }
                else
                {
                    $doc->update('sip.conf', $section, 'mailbox', $voicemail['mailbox'] .'@voicemail_' . $base['account_id']);
                }
            }
        }
    }

    public static function delete($base)
    {

    }

    public static function dialplan($number)
    {
        $doc = Telephony::getDriver()->doc;

        $destination = $number['Destination'];
        
        //voicemail_greeting_number

        $doc->add('Answer');

        $doc->add('Wait(1000)');

        if (!empty($number['registry']['skipGreeting']))
        {
            //$xml->update('/action[@application="set"][@data="skip_greeting=true"]');
        }

        if (!empty($number['registry']['skipInstructions']))
        {
            //$xml->update('/action[@application="set"][@data="skip_instructions=true"]');
        }

        $doc->add('Voicemail(' .$destination['mailbox'] .'@voicemail_' .$destination['account_id'] .')');
    }

    public static function prenumber()
    {

    }

    public static function postnumber()
    {
        $doc = Telephony::getDriver()->doc;

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

        $doc->add('Voicemail(' .$voicemail['mailbox'] .'@voicemail_' .$voicemail['account_id'] .')');
    }
}