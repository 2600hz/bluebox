<?php defined('SYSPATH') or die('No direct access allowed.');

class Asterisk_AutoAttendant_Driver extends Asterisk_Base_Driver
{
    public static function set($autoattendant)
    {

    }

    public static function delete($autoattendant)
    {
        
    }

    public static function dialplan($number)
    {
        $autoattendant = $number['Destination'];

        $doc = Telephony::getDriver()->doc;

        $doc->setCurrentNumber('i');

        $doc->add('Return', 1);

        $doc->setCurrentNumber('t');

        $doc->add('Return', 1);

        $doc->setCurrentNumber('h');

        $doc->add('Return', 1);

        foreach ($autoattendant['keys'] as $key)
        {
            if (!isset($key['digits']))
            {
                continue;
            }

            if($transfer = astrsk::getTransferToNumber($key['number_id']))
            {
                $doc->setCurrentNumber($key['digits']);

                $doc->add('Goto(' .$transfer .')', 1);
            }
        }

        // Don't forget to set the position back to where it was before
        $doc->setCurrentNumber('_X.');

        $doc->add('Answer', 1);

        $doc->add('Set(TIMEOUT(digit)=' .$autoattendant['digit_timeout'] . ')');

        $doc->add('Set(TIMEOUT(response)=' .$autoattendant['timeout'] . ')');

        if (!empty($autoattendant['registry']['max-failures']))
        {
            $doc->add('Set(f=' .$autoattendant['registry']['max-failures'] .')');
        }
        else
        {
            $doc->add('Set(f=3)');
        }

        if (empty($autoattendant['registry']['mediafile_id']) OR !class_exists('Media'))
        {
            $type = 'tty';
        }
        else
        {
            $type = $autoattendant['registry']['type'];
        }

        switch($type)
        {
            case 'audio':

                $doc->add('Background("' . Media::getFilePath($autoattendant['registry']['mediafile_id']) . '")', 'REPEAT');

                break;

            default:
                $doc->add('WaitExten(5)', 'REPEAT');
        }

        $doc->add('WaitExten(5)');

        $doc->add('Set(f=$[${f} - 1])');

        $doc->add('GotoIf($[${f} > 0]?REPEAT)');
    }
}