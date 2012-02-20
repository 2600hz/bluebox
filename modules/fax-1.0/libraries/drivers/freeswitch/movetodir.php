<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_movetodir_Driver extends Disposition_Driver
{
	public static function dialplan($number)
	{
		$xml = Telephony::getDriver()->xml;
        $faxProfile = $number['Destination'];
		
        $faxpackage = Doctrine::getTable('package')->findOneBy('name', 'fax');
		$xml->update('/action[@application="set"][@bluebox="settingHangupHook"][@data="api_hangup_hook=' . str_replace('/', '\/', DOCROOT .  '/' . $faxpackage->basedir . '/libraries/dispositions/movetodir.php ' . $faxProfile->fxp_spool_dir . '/infax-${uuid}.tif ' . $faxProfile['registry']['destination_dir'] . '/infax-${uuid}.tif"]'));
		
		parent::dialplan($number);
	}
}
?>