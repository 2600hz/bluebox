<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_dbndir_Driver extends FreeSwitch_Base_Driver
{
	public static function set($dbnData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="directory.conf"][@description="Directory"]';
		$xml->setXmlRoot($root);
		$updatestr = '/settings';
		$xml->update($updatestr);

		$root = '//document/section[@name="configuration"]/configuration[@name="directory.conf"][@description="Directory"]/profiles/profile[@name="dbnprofile_' . $dbnData->dbn_id . '"]';
		$xml->setXmlRoot($root);

		if (isset($dbnData->dbn_max_menu_attempts) && !empty($dbnData->dbn_max_menu_attempts) && $dbnData->dbn_max_menu_attempts != '')
			$updatestr = '/param[@name="max-menu-attempts"]{@value="' . $dbnData->dbn_max_menu_attempts . '"}';
		else
			$updatestr = '/param[@name="max-menu-attempts"]{@value="3"}';
			
		Kohana::log('debug', $updatestr);
		
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_min_search_digits) && !empty($dbnData->dbn_min_search_digits) && $dbnData->dbn_min_search_digits != '')
			$updatestr = '/param[@name="min-search-digits"]{@value="' . $dbnData->dbn_min_search_digits . '"}';
		else
			$updatestr = '/param[@name="min-search-digits"]{@value="3"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_terminator_key) && !empty($dbnData->dbn_terminator_key) && $dbnData->dbn_terminator_key != '')
			$updatestr = '/param[@name="terminator-key"]{@value="' . $dbnData->dbn_terminator_key . '"}';
		else
			$updatestr = '/param[@name="terminator-key"]{@value="#"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_digit_timeout) && !empty($dbnData->dbn_digit_timeout) && $dbnData->dbn_digit_timeout != '')
			$updatestr = '/param[@name="digit-timeout"]{@value="' . $dbnData->dbn_digit_timeout . '"}';
		else
			$updatestr = '/param[@name="digit-timeout"]{@value="3000"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_max_result) && !empty($dbnData->dbn_max_result) && $dbnData->dbn_max_result != '')
			$updatestr = '/param[@name="max-result"]{@value="' . $dbnData->dbn_max_result . '"}';
		else
			$updatestr = '/param[@name="max-result"]{@value="5"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_next_key) && !empty($dbnData->dbn_next_key) && $dbnData->dbn_next_key != '')
			$updatestr = '/param[@name="next-key"]{@value="' . $dbnData->dbn_next_key . '"}';
		else
			$updatestr = '/param[@name="next-key"]{@value="6"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_prev_key) && !empty($dbnData->dbn_prev_key) && $dbnData->dbn_prev_key != '')
			$updatestr = '/param[@name="prev-key"]{@value="' . $dbnData->dbn_prev_key . '"}';
		else
			$updatestr = '/param[@name="prev-key"]{@value="4"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_switch_order_key) && !empty($dbnData->dbn_switch_order_key) && $dbnData->dbn_switch_order_key != '')
			$updatestr = '/param[@name="switch-order-key"]{@value="' . $dbnData->dbn_switch_order_key . '"}';
		else
			$updatestr = '/param[@name="switch-order-key"]{@value="*"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_select_name_key) && !empty($dbnData->dbn_select_name_key) && $dbnData->dbn_select_name_key != '')
			$updatestr = '/param[@name="select-name-key"]{@value="' . $dbnData->dbn_select_name_key . '"}';
		else
			$updatestr = '/param[@name="select-name-key"]{@value="1"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_new_search_key) && !empty($dbnData->dbn_new_search_key) && $dbnData->dbn_new_search_key != '')
			$updatestr = '/param[@name="new-search-key"]{@value="' . $dbnData->dbn_new_search_key . '"}';
		else
			$updatestr = '/param[@name="new-search-key"]{@value="3"}';
		$xml->update($updatestr);
			
		if (isset($dbnData->dbn_search_order) && !empty($dbnData->dbn_search_order) && $dbnData->dbn_search_order != '')
			$updatestr = '/param[@name="search-order"]{@value="' . $dbnData->dbn_search_order . '"}';
		else
			$updatestr = '/param[@name="search-order"]{@value="last_name"}';
		$xml->update($updatestr);
	}

	public static function delete($dbnData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="directory.conf"][@description="Directory"]/profiles/';
		$xml->setXmlRoot($root);
		$xml->deleteNode('/profile[@name="dbnprofile_' . $dbnData->dbn_id . '"]');
	}
	
	public static function dialplan($Number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $Number['Destination'];
        
        $xmlText = '<action application="directory" data="dbnprofile_' . $destination['dbn_id'] . ' $${domain} context_' . $Number->plugins['dbndir']['tran_context_id'] . '"/>';
        $xml->replaceWithXml($xmlText);
	}
	
}
