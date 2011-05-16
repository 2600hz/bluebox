<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrholdmusic_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = <<<XML

<action application="answer"/>
<action application="playback" data="\$\${hold_music}"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}


?>