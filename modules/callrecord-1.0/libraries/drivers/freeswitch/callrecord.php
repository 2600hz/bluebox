<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_CallRecord_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        $callrecord = $base['plugins']['callrecord'];

        if ($base instanceof Trunk)
        {
            // Dont think there's much to do here - maybe someday
        }
        elseif ($base instanceof Conference) {
            // Need to implement this
        }
        elseif ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            // These vars are made up by this library. They are used consistently throughout.
            if (!empty($callrecord['inbound']))
            {
                $xml->update('/variables/variable[@name="callrecord_inbound"]{@value="' .$callrecord['inbound'] .'"}');
            } else {
                $xml->deleteNode('/variables/variable[@name="callrecord_inbound"]');
            }

            if (!empty($callrecord['outbound']))
            {
                $xml->update('/variables/variable[@name="callrecord_outbound"]{@value="' .$callrecord['outbound'] .'"}');
            } else {
                $xml->deleteNode('/variables/variable[@name="callrecord_outbound"]');
            }

        }
    }

    public static function delete($base)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

/*        if ($base instanceof Conference)
        {
            FreeSwitch::section('conference_profile', $base['conference_id']);

            $xml->deleteNode('/param[@name="caller-id-number"]');
        }
        else*/
        if ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->deleteNode('/variables/variable[@name="callrecord_inbound"]');

            $xml->deleteNode('/variables/variable[@name="callrecord_outbound"]');
        }
    }

    public static function conditioning()
    {

        $xml = FreeSWITCH::createExtension('callrecord');

        // We'll always set the params for recorded calls, regardless of if they ever get used.
        $content = <<<XML

      <condition>
        <action application="set" data="RECORD_TITLE=Recording \${destination_number} \${caller_id_number} \${strftime(%Y-%m-%d %H:%M)}"/>
        <action application="set" data="RECORD_COPYRIGHT=(c) 2010 VoIP, Inc."/>
        <action application="set" data="RECORD_SOFTWARE=blue.box"/>
        <action application="set" data="RECORD_ARTIST=2600hz"/>
        <action application="set" data="RECORD_COMMENT=Automatically recorded via FreeSWITCH with blue.box"/>
        <action application="set" data="RECORD_DATE=\${strftime(%Y-%m-%d %H:%M)}"/>
        <action application="set" data="RECORD_STEREO=true"/>
      </condition>

XML;

        $xml->replaceWithXml($content);
    }

    public static function preAnswer()
    {

        // Record any outbound calls, if the user is auth'd and the flag is set
        $xml = FreeSWITCH::createExtension('callrecord_outbound');

        $filename = Kohana::config('freeswitch.audio_root') . '/recordings/${strftime(%Y-%m-%d-%H-%M-%S)}_${destination_number}_${caller_id_number}.wav';

        // We'll always set the params for recorded calls, regardless of if they ever get used.
        $content = <<<XML

      <condition field="\${record_outbound}" expression="^.+$">
        <action application="record_session" data="$filename"/>
      </condition>

XML;

        $xml->replaceWithXml($content);

    }

    public static function preNumber()
    {
        $number = Event::$data;
        
        $xml = Telephony::getDriver()->xml;

        $callrecord = $number['Destination']['plugins']['callrecord'];
                
        if (!$callrecord)
        {
            return;
        }

        // Is this a trunk? If so, add generic stuff to see if this is a
        if ($number['Destination'] instanceof Device)
        // Is inbound call recording set? If so, record calls to this user
        if (!empty($callrecord['inbound']) and ($callrecord['inbound']))
        {
            $filename = Kohana::config('freeswitch.audio_root') . '/recordings/${strftime(%Y-%m-%d-%H-%M-%S)}_${destination_number}_${caller_id_number}.wav';
            $xml->update('/action[@application="record_session"][@bluebox="callrecord_action"][@data="' . str_replace('/', '\/', $filename) . '"]');
        }
        else
        {
            $xml->deleteNode('/action[@application="set"][@bluebox="callrecord_action"]');
        }
    }
}
