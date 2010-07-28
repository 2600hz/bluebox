<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_AutoAttendant_Driver extends FreeSwitch_Base_Driver
{
    public static function set($autoattendant)
    {
        $xml = FreeSwitch::setSection('autoattendant', $autoattendant['auto_attendant_id']);
        
        switch($autoattendant['registry']['type'])
        {
            case 'audio':

                $xml->setAttributeValue('', 'greet-long', Media::getFilePath($autoattendant['registry']['mediafile_id']));

                $xml->setAttributeValue('', 'greet-short', Media::getFilePath($autoattendant['registry']['mediafile_id']));

                break;

            default:
                if (empty($autoattendant['registry']['tts_string']))
                {
                    $tts_string = 'Thank you for calling, your call is important to us.';
                }
                else
                {
                    $tts_string = $autoattendant['registry']['tts_string'];
                }

                $tts_string = 'say:' .preg_replace('/[^A-Za-z0-9.,!? ]/', '', $tts_string);

                $xml->setAttributeValue('', 'tts-engine', 'cepstral');

                $xml->setAttributeValue('', 'tts-voice', 'Allison-8kHz');

                $xml->setAttributeValue('', 'greet-long', $tts_string);

                $xml->setAttributeValue('', 'greet-short', $tts_string);
        }

        if (!empty($autoattendant['extension_context_id']))
        {
            $xml->setAttributeValue('', 'digit-len', $autoattendant['extension_digits']);
        }

        $xml->deleteChildren();

        if (!empty($autoattendant['extension_context_id']))
        {
           $xml->update(sprintf('/entry[@action="menu-exec-app"][@name="catch_all"][@digits="\/^([0-9]{%s})$\/"][@param="execute_extension $1 XML context_%s"]', $autoattendant['extension_digits'], $autoattendant['extension_context_id']));
        }

        foreach ($autoattendant['keys'] as $key)
        {
            if (empty($key['digits']))
            {
                continue;
            }

            if(!($transferString = fs::getTransferToNumber($key['number_id'])))
            {
                continue;
            }

            $xml->update(sprintf('/entry[@action="menu-exec-app"][@digits="%s"][@param="transfer %s"]', $key['digits'], $transferString));
        }
    }

    public static function delete($autoattendant)
    {
        FreeSwitch::setSection('autoattendant', $autoattendant['auto_attendant_id'])->deleteNode();
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        
        $destination = $number['Destination'];

//        $ringtype = ($number->options['ringtype'] == 'Ringing' ? "us-ring" : 'moh');
//
//        $xml->update('/action[@application="set"][@bluebox="ring"]{@data="ringback=${' . $ringtype . '}"}');
//
//        $xml->update('/action[@application="set"][@bluebox="xfer-ring"]{@data="transfer_ringback=${' . $ringtype . '}"}');

        $xml->update('/action[@application="answer"]');

        $xml->update('/action[@application="ivr"]{@data="auto_attendant_' .$destination['auto_attendant_id'] .'"}');
    }
}
