<?php defined('SYSPATH') or die('No direct access allowed.');

class delay_echo_FreeSwitch_Feature_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = <<<XML

<action application="answer"/>
<action application="delay_echo" data="1000"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}


?>