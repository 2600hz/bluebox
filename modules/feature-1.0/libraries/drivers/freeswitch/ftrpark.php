<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrpark_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = <<<XML

        <action application="answer"/>
        <action application="sleep" data="1000"/>
        <action application="valet_park" data="account_${destination['account_id']} auto in 1 10"/>
        <action application="hangup"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}

?>