<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_callcenter_settings_Driver extends FreeSwitch_Base_Driver
{
	public static function set($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/settings';
		$xml->setXmlRoot($root);

		if ($ccData->cc_odbc_dsn == 0)
		$xml->deleteNode('/param[@name="odbc-dsn"]');
		else
		{
			$odbcTable = Doctrine::getTable('Odbc');
			$odbcObj = $odbcTable->find($ccData->cc_odbc_dsn);
			$xml->update('/param[@name="odbc-dsn"]{@value="' . $odbcObj->dsn_name . '"}');
		}
		if (!isset($ccData->cc_db_name) || empty($ccData->cc_db_name) || $ccData->cc_db_name == '')
		$xml->deleteNode('/param[@name="dbname"]');
		else
		$xml->update('/param[@name="dbname"]{@value="' . $ccData->cc_db_name . '"}');
	}

	public static function delete($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

		$xml->setXmlRoot($root);
		$xml->deleteNode();
	}

	public static function reload()
	{
		$eslMan = new EslManager();
		$eslCon = $eslMan->getESL();
		try {
			$responsestr = $eslCon->sendRecv('api reload mod_callcenter')->getBody();
		} catch (ESLException $e) {
			if (strpos($e->getMessage(), 'Not connected'))
			{
				throw new callcenterException('<div class="error">Unable to connect to the switch console.  This usually means that mod_event_socket is not running, or that the password that you entered during the setup process has changed. The error message was:<br>' . $e->getMessage() .'</div>');
			}
			else
				throw $e;
		}
		
		$responses = explode('', $responsestr);
		
		Kohana::log('debug', print_r($responses, true));
		if (substr(trim($responses[count($responses)-1]), 0, 4) == '-ERR')
			$this->_throwError($responsestr);
			
		return $responsestr;
	}
}
?>