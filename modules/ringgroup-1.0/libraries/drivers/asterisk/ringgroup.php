<?php defined('SYSPATH') or die('No direct access allowed.');

class Asterisk_RingGroup_Driver extends Asterisk_Base_Driver
{
    public static function set($ringgroup)
    {

    }

    public static function delete($ringgroup)
    {

    }

    public static function dialplan($number)
    {
        $dialstring = '';

        $memberSeperator = '&';

        $destination = $number['Destination'];

        $doc = Telephony::getDriver()->doc;

        $numberOptions = astrsk::getNumberOptions($number);

        foreach ($destination['members'] as $member)
        {
            $dialstring .= $member['bridge'] .$memberSeperator;
        }

        $dialstring = rtrim($dialstring, $memberSeperator);

        $doc->add('Dial(' .$dialstring .',' .$numberOptions['timeout'] .')');
    }
}