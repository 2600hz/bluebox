<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_ftrintercom_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
		$xml->setXmlRoot($xml->getExtensionRoot() .'/condition[@field="destination_number"][@expression="^' . $number['number'] . '(.+)"][@break="never"]');
		$xmlText = '        
		 <action application="set" data="dialed_ext=$1"/>
         <action application="export"><![CDATA[alert_info=<sip:$${location_' . $number->location_id . '}>;Ring;Answer]]></action>
         <action application="export"><![CDATA[sip_h_Call-Info=<sip:$${location_' . $number->location_id . '}>;answer-after=0]]></action>
         <action application="export" data="sip_invite_params=intercom=true"/>
         <action application="export" data="sip_auto_answer=true"/>
         <action application="export" data="sip_auto_answer=true"/>
         <action application="bridge" data="user/\${dialed_ext}@$${location_' . $number->location_id . '}"/>
';
        $xml->replaceWithXml($xmlText);
	}
}

?>