<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_ftrtonetest_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = <<<XML

<action application="answer"/>
<action application="playback" data="tone_stream://%(1000,0,2600);loops=-1"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}
?>