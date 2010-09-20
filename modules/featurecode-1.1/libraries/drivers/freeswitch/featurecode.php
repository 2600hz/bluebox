<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj) 
    {

    }

    public static function delete($obj) 
    {

    }

    public static function dialplan($number) {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];

        $vmdomain = 'voicemail_' .$destination['account_id'];
        $domain = 'location_' . $destination['location_id'];
        
        switch ($registry['feature']) {
            case 'forward_on':
                $xmlText = <<<XML
XML;
            
                break;
            
            case 'forward_off':
                $xmlText = <<<XML
XML;
                break;
            
            case 'redial':
                $xmlText = <<<XML
<action application="transfer" data="\${hash(select/\${domain_name}-last_dial/\${caller_id_number})}"/>
XML;
                break;
            
            case 'call_return':
                $xmlText = <<<XML
<action application="transfer" data="\${hash(select/\${domain_name}-call_return/\${caller_id_number})}"/>
XML;
                break;
            
            case 'voicemail':
                $xmlText = <<<XML
<action application="answer"/>
<action application="sleep" data="1000"/>
<action application="voicemail" data="check default $vmdomain"/>
XML;
                break;

            // TODO: This is ghetto - should be changed to use mwi-account variable and split it apart w/ regexs
            // That would allow voicemail #s to not match Caller ID #s AND would work with shared mailboxes AND would provide
            // an extra layer of security. FIX.
            case 'voicemail_quickauth':
                $xmlText = <<<XML
<action application="answer"/>
<action application="sleep" data="1000"/>
<action application="voicemail" data="check default $vmdomain \${caller_id_number}"/>
XML;
                break;

            case 'voicemail_noauth':
                $xmlText = <<<XML
<action application="answer"/>
<action application="sleep" data="1000"/>
<action application="set" data="voicemail_authorized=\${sip_authorized}"/>
<action application="voicemail" data="check default $vmdomain \${caller_id_number}"/>
XML;
                break;
            
            case 'park':
                $xmlText = <<<XML
<action application="set" data="fifo_music=\$\${hold_music}"/>
<action application="fifo" data="general@$domain in"/>
XML;
                break;
            
            case 'unpark':
                $xmlText = <<<XML
<action application="answer"/>
<action application="fifo" data="general@$domain out nowait"/>
XML;
                break;
            
            case 'echo':
                $xmlText = <<<XML
<action application="answer"/>
<action application="echo"/>
XML;
                break;
            
            case 'delay_echo':
                $xmlText = <<<XML
<action application="answer"/>
<action application="delay_echo" data="5000"/>
XML;
                break;
            
            case 'tone_test':
                $xmlText = <<<XML
<action application="answer"/>
<action application="playback" data="tone_stream://%(251,0,1004);loops=-1"/>
XML;
                break;
            
            case 'hold_music':
                $xmlText = <<<XML
<action application="answer"/>
<action application="playback" data="\$\${hold_music}"/>
XML;
                break;
            }

        $xml->replaceWithXml($xmlText);

    }

    public function postRoute() {
    {
        $xml = FreeSWITCH::createExtension('global');

        // This XML code will track the number dialed, the caller ID of the last inbound call and/or some other basic info
        $newXml = <<<XML
      <condition>
        <action application="hash" data="insert/\${domain_name}-spymap/\${caller_id_number}/\${uuid}"/>
        <action application="hash" data="insert/\${domain_name}-last_dial/\${caller_id_number}/\${destination_number}"/>
        <action application="hash" data="insert/\${domain_name}-last_dial/global/\${uuid}"/>
        <action application="set" data="RFC2822_DATE=\${strftime(%a, %d %b %Y %T %z)}"/>
      </condition>

XML;

        $xml->replaceWithXml($newXml);
    }

    }
}
