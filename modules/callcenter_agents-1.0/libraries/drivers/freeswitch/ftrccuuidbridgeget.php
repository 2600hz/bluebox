<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrccuuidbridgeget_Driver extends FreeSwitch_Feature_Driver
                 ftrccuuidbridgeget
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = '
    <action application="play_and_get_digits" data="3 15 3 7000 # $${base_dir}/sounds/en/us/callie/voicemail/vm-enter_id.wav $${base_dir}/sounds/en/us/callie/voicemail/vm-fail_auth.wav agentid \d+"/>
    <action application="set" data="res=${callcenter_config(agent set uuid ${agentid}@${domain_name} \'${uuid}\')}" />
	<action application="set" data="res=${callcenter_config(agent set type ${agentid}@${domain_name} \'uuid-standby\')}" />
	<action application="set" data="res=${callcenter_config(agent set status ${agentid}@${domain_name} \'Available (On Demand)\')}" />
	<action application="set" data="res=${callcenter_config(agent set state ${agentid}@${domain_name} \'Waiting\')}" />
	<action application="set" data="cc_warning_tone=tone_stream://%(200,0,500,600,700)"/>
	<action application="answer" />
	<action application="playback" data="$${hold_music}"/>
	<action application="transfer" data="' . $number['number'] . '"/>
	';
        $xml->replaceWithXml($xmlText);
	}
}

?>