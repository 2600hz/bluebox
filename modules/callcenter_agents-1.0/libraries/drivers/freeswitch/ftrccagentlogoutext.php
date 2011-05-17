<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrccagentlogoutext_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = '
  <action application="set" data="res=${callcenter_config(agent set status ${caller_id_number}@${domain_name} \'Logged Out\')}" />
  <action application="answer" data=""/>
  <action application="sleep" data="500"/>
  <action application="playback" data="ivr/ivr-you_are_now_logged_out.wav"/>
  <action application="hangup" data=""/>
  ';
        $xml->replaceWithXml($xmlText);
	}
}

?>