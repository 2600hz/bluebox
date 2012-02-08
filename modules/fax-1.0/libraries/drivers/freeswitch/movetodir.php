<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_movetodir_Driver extends Disposition_Driver
{
	public static function preNumber()
	{
		$xml = Telephony::getDriver()->xml;
		$number = Event::$data;
        $faxProfile = $number['destination'];
		
        $faxpackage = Doctrine::getTable('package')->findOneBy('name', 'fax');
		$xml->update('/action[@application="set"][@bluebox="settingHangupHook"][@data="api_hangup_hook=' . DOCROOT .  '/ ' . $faxpackage->basedir . '/libraries/dispositions/movetodir.php -s ' . $faxProfile->fxp_spool_dir . '/infax-${uuid}.tif ' . $faxProfile['registry']['destination_dir'] . '/infax-${uuid}.tif"]');
		
		parent::preNumber();
	}
}
?>