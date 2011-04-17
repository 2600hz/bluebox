<?php defined('SYSPATH') or die('No direct access allowed.');

class checkvmaskuseraskpass_FreeSwitch_Feature_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $vmdomain = 'voicemail_' .$destination['account_id'];

		$xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="voicemail" data="check default $vmdomain"/>
        <action application="hangup"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}

?>