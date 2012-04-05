<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_emailtouser_Driver extends Disposition_Driver
{
	public static function dialplan($number)
	{	
		$xml = Telephony::getDriver()->xml;
        $faxProfile = $number['Destination'];
		
		$faxpackage = Doctrine::getTable('package')->findOneBy('name', 'fax');
		$dpentry = DOCROOT .  '/ ' . $faxpackage->basedir . '/libraries/dispositions/sendasemail.php';
		if (isset($faxProfile['registry']['admin_email']) && $faxProfile['registry']['admin_email'] != '')
			$dpentry .= ' -s ' . $faxProfile['registry']['admin_email'];

		if (isset($faxProile['registry']['from_address']) && $faxProile['registry']['from_address'] != '')
			$dpentry .= ' -f ' . $faxProfile['registry']['from_address'];
			
		if (isset($faxProfile['registry']['from_name']) && $faxProfile['registry']['from_name'] != '')
			$dpentry .= ' -n \'' . $faxProfile['registry']['from_name'] . '\'';
		
		if (isset($faxProfile['registry']['email_subject']) && $faxProfile['registry']['email_subject'] != '')
			$dpentry .= ' -u \'' . $faxProfile['registry']['email_subject'] . '\'';
		
		if (isset($faxProfile['registry']['email_body']) && $faxProfile['registry']['email_body'] != '')
			$dpentry .= ' -b \'' . $faxProfile['registry']['email_body'] . '\'';
			
		if (isset($faxProfile['registry']['dest_email']) && $faxProfile['registry']['dest_email'] != '')
			$dpentry .=  ' -d \'' . $faxProfile['registry']['dest_email'] . '\'';
		else
			$dpentry .=  ' -d \'${dialedEmail}\'';
		
		if (isset($faxProfile['registry']['send_status']) && !$faxProfile['registry']['send_status'])
			$dpentry .= ' -e';
			
		$dpentry .= ' -ocidname \'${caller_id_name}\' -ocidnum \'${caller_id_number}\' -r \\\${fax_result_code} -t \'\\\${fax_result_text}\' -i \'\\\${fax_remote_station_id}\' -pr \\\${fax_document_transferred_pages} -pt \\\${fax_document_total_pages} ' . $faxProfile->fxp_spool_dir . '/infax-${uuid}.tiff';
		Kohana::log('debug', $dpentry);
		
		$xml->update('/action[@application="set"][@bluebox="settingHangupHook"][@data="api_hangup_hook=' . str_replace(array('/', '@', '$', '{', '}', '-'), array('\/', '\@', '\$', '\{', '\}', '\-'), $dpentry) .'"]');
		
		parent::dialplan($number);
	}
	
	
	public static function prenumber($number)
	{
        $xml = Telephony::getDriver()->xml;
        $number = Event::$data;
 
        $destination = $number['Destination'];
        if (!($destination instanceof FaxProfile))
        {
        	$domain = '$${location_' . $destination['User']['location_id'] . '}';
        	$dialeduser = $destination['plugins']['sip']['username'] .'@' . $domain;

        	$xml->update('/action[@application="set"][@bluebox="settingDialeduser"]{@data="dialedUser=' . $dialeduser . '"}');
        }
        
        parent::prenumber($number);
	}

    public static function conditioning()
    {
        $xml = FreeSWITCH::createExtension('faxAutodetect');
		$condition = '/condition[@field="${dialedUser}"][@expression="^(.*)$"]';
		$xml->update($condition . '/action[@application="set"][@bluebox="settingDialedMWI"][@data="dialedMWI=user_data \'$1 param mwi-account\'"]');
		$xml->update($condition . '/action[@application="set"][@bluebox="settingDialedEmail"][@data="dialedEmail=user_data \'${dialedMWI} param email-addr\'"]');
		
		parent::conditioning();
    }
}

?>