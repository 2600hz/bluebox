<?php defined('SYSPATH') or die('No direct access allowed.');

class callreturn_FreeSwitch_Feature_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = <<<XML

<action application="transfer" data="\${hash(select/\${domain_name}-call_return/\${caller_id_number})}"/>

XML;
        $xml->replaceWithXml($xmlText);
	}
}

?>