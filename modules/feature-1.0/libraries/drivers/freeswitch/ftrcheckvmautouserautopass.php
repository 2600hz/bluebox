<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrcheckvmautouserautopass_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
		$vmdomain = 'voicemail_' .$destination['account_id'];
		$xml->setXmlRoot($xml->getExtensionRoot());
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
        $xml->replaceWithXml($xmlText);
	}
}


?>