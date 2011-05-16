<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrforwardoff_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];

        $xmlText = <<<XML


XML;
        $xml->replaceWithXml($xmlText);
	}
}

?>