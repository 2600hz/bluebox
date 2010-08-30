<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Conference_Driver extends FreeSwitch_Base_Driver
{
    public static function set($conference)
    {
        $xml = FreeSwitch::setSection('conference_profile', $conference['conference_id']);

        $xml->deleteChildren();

        $profile = arr::merge(Conference::$default_profile, $conference['profile']);
        $registry = $conference['registry'];

        foreach ($profile as $parameter => $value)
        {
            $value = str_replace('/', '\/', $value);
            
            if (isset($registry[$parameter])) {
                $value = $registry[$parameter];
            }

            $xml->update('/param[@name="' .$parameter .'"]{@value="' .$value .'"}');
        }

        if( ! empty($registry['moh_type'])) {
            $value = str_replace('/', '\/', $registry['moh_type']);
            
            $xml->update('/param[@name="moh-sound"]{@value="' . $value .'"}');
        }
        if (empty($conference['pins']))
        {
            return;
        }
        
        foreach ($conference['pins'] as $pin)
        {
            if (empty($pin))
            {
                continue;
            }
            
            $xml->update('/param[@name="pin"]{@value="' .$pin .'"}');
        }

    }

    public static function delete($conference)
    {
        $xml = FreeSwitch::setSection('conference_profile', $conference['conference_id']);

        $xml->deleteNode();
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        $xml->update('/action[@application="export"][@data="hold_music=silence"]');

        $xml->update('/action[@application="answer"]');

        // Pause for a second on answer, to avoid audio clipping
        $xml->update('/action[@application="sleep"]{@data="1000"}');
        
        $xml->update('/action[@application="conference"]{@data="conference_' .$destination['conference_id'] . '@conference_' .$destination['conference_id'] .'"}');
    }
}
