<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj) 
    {

    }

    public static function delete($obj) 
    {

    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        $registry = (array)$destination['registry'];

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

                $vmdomain = 'voicemail_' .$destination['account_id'];

                $xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="voicemail" data="check default $vmdomain"/>
        <action application="hangup"/>

XML;
                break;

            case 'voicemail_quickauth':

                $vmdomain = 'voicemail_' .$destination['account_id'];

	 	$xml->setXmlRoot($xml->getExtensionRoot() .'/condition[@field="${user_data(${sip_auth_username}@${sip_auth_realm} param mwi-account)}"][@expression="^(.+)@(.+)$"]');

                $xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="voicemail" data="check default \$2 \$1"/>
        <action application="hangup"/>
        <anti-action application="answer"/>
        <anti-action application="sleep" data="1000"/>
        <anti-action application="voicemail" data="check default $vmdomain"/>
        <anti-action application="hangup"/>

XML;
                break;

            case 'voicemail_noauth':
                $vmdomain = 'voicemail_' .$destination['account_id'];

	 	$xml->setXmlRoot($xml->getExtensionRoot() .'/condition[@field="${user_data(${sip_auth_username}@${sip_auth_realm} param mwi-account)}"][@expression="^(.+)@(.+)$"]');

                $xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="set" data="voicemail_authorized=\${sip_authorized}"/>
        <action application="voicemail" data="check default \$2 \$1"/>
        <action application="hangup"/>
        <anti-action application="answer"/>
        <anti-action application="sleep" data="1000"/>
        <anti-action application="voicemail" data="check default $vmdomain"/>
        <anti-action application="hangup"/>

XML;
                break;
            
            case 'park':
                $xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="valet_park" data="account_${destination['account_id']} auto in 1 10"/>
        <action application="hangup"/>

XML;
                break;
            
            case 'unpark':
                $xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="valet_park" data="account_${destination['account_id']} ask 1 10 10000 ivr/ivr-enter_ext_pound.wav"/>
        <action application="hangup"/>

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
