<?php defined('SYSPATH') or die('No direct access allowed.');

class ivrreturn_FreeSwitch_Feature_Driver extends FreeSwitch_Feature_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
		$xml->deleteChildren();

		$condition = '/condition[@field="${ivr_path}"][@expression="(.*)-(.*)-.*+$"][@break="never"]';

		$xml->setXmlRoot($xml->getExtensionRoot() .$condition);

		$xml->update('/action[@application="set"][@data="ivr_path=$1"]');
		$xml->update('/action[@application="transfer"][@data="$2"]');

		$xml->update('/anti-action[@application="set"][@data="ivr_path="]');
		$xml->update('/anti-action[@application="transfer"][@data="${vm-operator-extension}"]');
	}
}

?>