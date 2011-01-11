<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_AutoAttendant_Driver extends FreeSwitch_Base_Driver
{
    public static function set($autoattendant)
    {
        $xml = FreeSwitch::setSection('autoattendant', $autoattendant['auto_attendant_id']);

        media::generateConfig($autoattendant, $xml);

        if (!empty($autoattendant['extension_context_id']))
        {
            $xml->setAttributeValue('', 'digit-len', $autoattendant['extension_digits']);
        }

        $xml->setAttributeValue('', 'timeout', $autoattendant['timeout'] * 1000);

        $xml->setAttributeValue('', 'inter-digit-timeout', $autoattendant['digit_timeout'] * 1000);

        if (!empty($autoattendant['registry']['max-failures']))
        {
            $xml->setAttributeValue('', 'max-failures', $autoattendant['registry']['max-failures']);
        }
        else
        {
            $xml->setAttributeValue('', 'max-failures', '3');
        }
        
        $xml->deleteChildren();

        if (!empty($autoattendant['extension_context_id']))
        {
           $xml->update(sprintf('/entry[@action="menu-exec-app"][@name="catch_all"][@digits="\/^([0-9]{%s})$\/"][@param="execute_extension $1 XML context_%s"]', $autoattendant['extension_digits'], $autoattendant['extension_context_id']));
        }
        
        foreach ($autoattendant['keys'] as $key)
        {
            if (!isset($key['digits']))
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

        $xml->update('/action[@application="answer"]');

        $xml->update('/action[@application="set"][@data="ivr_path=${ivr_path}-${destination_number} XML ${context}"]');

        $xml->update('/action[@application="ivr"]{@data="auto_attendant_' .$destination['auto_attendant_id'] .'"}');
    }
}
