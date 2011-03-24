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
            case 'ivr_return':
                $xml->deleteChildren();

                $condition = '/condition[@field="${ivr_path}"][@expression="(.*)-(.*)-.*+$"][@break="never"]';

                $xml->setXmlRoot($xml->getExtensionRoot() .$condition);

                $xml->update('/action[@application="set"][@data="ivr_path=$1"]');
                $xml->update('/action[@application="transfer"][@data="$2"]');

                $xml->update('/anti-action[@application="set"][@data="ivr_path="]');
                $xml->update('/anti-action[@application="transfer"][@data="${vm-operator-extension}"]');

                break;

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

                $xml->setXmlRoot($xml->getExtensionRoot() .'/condition[@field="${user_data(${sip_from_user}@${sip_from_host} param mwi-account)}"][@expression="^(.+)@(.+)$"]');

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

                $xml->setXmlRoot($xml->getExtensionRoot());

                $xml->setXmlRoot($xml->getExtensionRoot() .'/condition[@field="${user_data(${sip_from_user}@${sip_from_host} param mwi-account)}"][@expression="^(.+)@(.+)$"]');

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
<action application="delay_echo" data="1000"/>
XML;
                break;
            
            case 'tone_test':
                $xmlText = <<<XML
<action application="answer"/>
<action application="playback" data="tone_stream://%(1000,0,2600);loops=-1"/>
XML;
                break;
            
            case 'hold_music':
                $xmlText = <<<XML
<action application="answer"/>
<action application="playback" data="\$\${hold_music}"/>
XML;
                break;

            case 'eavesdrop' :
                $xmlText = <<<XML
XML;

            case 'uuid_standby':
                $num = $number['number'];

                $xmlText = <<<XML
    <action application="set" data="res=\${callcenter_config(agent set uuid agent_\${agent_id} '\${uuid}')}" />
    <action application="set" data="res=\${callcenter_config(agent set type agent_\${agent_id} 'uuid-standby')}" />
    <action application="set" data="res=\${callcenter_config(agent set status agent_\${agent_id} 'Available (On Demand)')}" />
    <action application="set" data="res=\${callcenter_config(agent set state agent_\${agent_id} 'Waiting')}" />
    <action application="set" data="cc_warning_tone=tone_stream://%(200,0,500,600,700)"/>
    <action application="answer" />
    <action application="playback" data="\$\${hold_music}"/>
    <action application="transfer" data="$num"/>

XML;
                break;

            case 'agent_login':
                $xmlText = <<<XML
    <action application="set" data="res=\${callcenter_config(agent set status agent_\${agent_id} 'Available')}" />
    <action application="answer" data=""/>
    <action application="sleep" data="500"/>
    <action application="playback" data="ivr/ivr-you_are_now_logged_in.wav"/>
    <action application="hangup" data=""/>

XML;
                break;

            case 'agent_logout':
                $xmlText = <<<XML
    <action application="set" data="res=\${callcenter_config(agent set status agent_\${agent_id} 'Logged Out')}" />
    <action application="answer" data=""/>
    <action application="sleep" data="500"/>
    <action application="playback" data="ivr/ivr-you_are_now_logged_out.wav"/>
    <action application="hangup" data=""/>

XML;
                break;
            }

        if (isset($xmlText))
        {
            $xml->replaceWithXml($xmlText);
        }

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
