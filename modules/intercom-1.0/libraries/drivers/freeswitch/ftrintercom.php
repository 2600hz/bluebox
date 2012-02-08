<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_ftrintercom_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        
		$xml->setXmlRoot($xml->getExtensionRoot());
		$condition = '/condition[@field="destination_number"]{@expression="^' . str_replace(array('*','+'), array('\*','\+'), $number['number']) . '(.+)$"}';
		$xml->update($condition);
		$condition = '/condition[@field="destination_number"][@expression="^' . str_replace(array('*','+'), array('\*','\+'), $number['number']) . '(.+)$"]';
		$xml->setXmlRoot($xml->getExtensionRoot() . $condition);
		$xml->update('/action[@application="export"][@data="sip_auto_answer=true"]');
		$xml->update('/action[@application="bridge"][@data="loopback\/$1"]');
	}
}

?>