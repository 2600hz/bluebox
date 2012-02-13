<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrccagentloginget_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = '
    <action application="play_and_get_digits" data="3 15 3 7000 # $${base_dir}/sounds/en/us/callie/voicemail/vm-enter_id.wav $${base_dir}/sounds/en/us/callie/voicemail/vm-fail_auth.wav agentid \d+"/>
    <action application="set" data="res=${callcenter_config(agent set status ${agentid}@${domain_name} \'Available\')}" />
    <action application="answer" data=""/>
    <action application="sleep" data="500"/>
    <action application="playback" data="ivr/ivr-you_are_now_logged_in.wav"/>
    <action application="hangup" data=""/>
  ';
        $xml->replaceWithXml($xmlText);
	}
}

?>