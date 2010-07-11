<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Conference_Driver extends FreeSwitch_Base_Driver
{
    public static function set($conference)
    {
        $xml = FreeSwitch::setSection('conferences');

        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        $prefix = sprintf('/profiles/profile[@name="conference_%s"]', $conference['conference_id']);

        $profile = arr::merge(Conference::$default_profile, $conference['profile']);

        foreach ($profile as $parameter => $value)
        {
            $value = str_replace('/', '\/', $value);
            
            $xml->update($prefix .'/param[@name="' .$parameter .'"]{@value="' .$value .'"}');
        }
    }

    public static function delete($conference)
    {
        $xml = FreeSwitch::setSection('conferences');
       
        $xml->deleteNode(sprintf('/profiles/profile[@name="conference_%s"]', $conference['conference_id']));
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        $xml->update('/action[@application="answer"]');

        // Pause for a second on answer, to avoid audio clipping
        $xml->update('/action[@application="sleep"]{@data="1000"}');
        
        $xml->update('/action[@application="conference"]{@data="conference_' .$destination['conference_id'] . '@conference_' .$destination['conference_id'] .'"}');
    }
}
