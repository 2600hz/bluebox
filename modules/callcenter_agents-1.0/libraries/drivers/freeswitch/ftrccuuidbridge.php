<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrccuuidbridge_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = '
	<action application="set" data="res=${callcenter_config(agent set uuid ${caller_id_number}@${domain_name} \'${uuid}\')}" />
	<action application="set" data="res=${callcenter_config(agent set type ${caller_id_number}@${domain_name} \'uuid-standby\')}" />
	<action application="set" data="res=${callcenter_config(agent set status ${caller_id_number}@${domain_name} \'Available (On Demand)\')}" />
	<action application="set" data="res=${callcenter_config(agent set state ${caller_id_number}@${domain_name} \'Waiting\')}" />
	<action application="set" data="cc_warning_tone=tone_stream://%(200,0,500,600,700)"/>
	<action application="answer" />
	<action application="playback" data="$${hold_music}"/>
	<action application="transfer" data="' . $number['number'] . '"/>
	';
        $xml->replaceWithXml($xmlText);
	}
}

?>