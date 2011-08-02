<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ftrredial_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = <<<XML

<action application="transfer" data="\${hash(select/\${domain_name}-last_dial/\${caller_id_number})}"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}

?>