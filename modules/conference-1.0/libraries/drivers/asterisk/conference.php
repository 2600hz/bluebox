<?php defined('SYSPATH') or die('No direct access allowed.');

class Asterisk_Conference_Driver extends Asterisk_Base_Driver
{
    public static function set($conference)
    {

    }

    public static function delete($conference)
    {

    }

    public static function dialplan($number)
    {
        $conference = $number['Destination'];

        $pin = '';

        if (!empty($conference['pins'][0]))
        {
            $pin = ',' .$conference['pins'][0];
        }

        $options = '';

        if (!empty($conference['registry']['record']))
        {
           $options .= 'r';
        }

        if (!empty($conference['registry']['moh_type']) AND $conference['registry']['moh_type'] == 'local_stream://moh' )
        {
           $options .= 'M';
        }

        $doc = Telephony::getDriver()->doc;

        $doc->add('MeetMeCount(${CONTEXT},CONF_COUNT)');

        $doc->add('GotoIf($[${CONF_COUNT} = 0]?new:existing)');

        $doc->add('MeetMe(${CONTEXT},acs' .$options .$pin .')', 'new');

        $doc->add('Return');

        $doc->add('MeetMe(${CONTEXT},cs' .$options .$pin .')', 'existing');
    }
}