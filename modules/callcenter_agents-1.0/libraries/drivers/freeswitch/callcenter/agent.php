<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_callcenter_agent_Driver extends FreeSwitch_Base_Driver
{
	protected static $anInstance = null;

	public static function getInstance()
	{
		if (self::$anInstance == null)
			self::$anInstance = new FreeSwitch_callcenter_agent_Driver();
		return self::$anInstance;
	}

	public static function set($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/agents';
		$xml->setXmlRoot($root);
		$locationRec = Doctrine::getTable('Location')->find($ccData->cca_locationid);
		$ccData->cca_contact = str_replace('/', '\/', $ccData->cca_contact);

		$updatestr = '/agent[@bluebox="' . $ccData->cca_id . '"]{@name="' . $ccData->cca_loginid . '\@' . $locationRec->domain . '"}{@type="' . $ccData->cca_type . '"}{@contact="' . $ccData->cca_contact . '"}';

		if (isset($ccData->cca_status) && !empty($ccData->cca_status) && $ccData->cca_status != '')
				$updatestr .= '{@status="' . $ccData->cca_status . '"}';

		if (isset($ccData->cca_max_no_answer) && !empty($ccData->cca_max_no_answer) && $ccData->cca_max_no_answer != '')
				$updatestr .= '{@max-no-answer="' . $ccData->cca_max_no_answer . '"}';

		if (isset($ccData->cca_wrap_up_time) && !empty($ccData->cca_wrap_up_time) && $ccData->cca_wrap_up_time != '')
				$updatestr .= '{@wrap-up-time="' . $ccData->cca_wrap_up_time . '"}';

		if (isset($ccData->cca_reject_delay_time) && !empty($ccData->cca_reject_delay_time) && $ccData->cca_reject_delay_time != '')
				$updatestr .= '{@reject-delay-time="' . $ccData->cca_reject_delay_time . '"}';

		if (isset($ccData->cca_busy_delay_time) && !empty($ccData->cca_busy_delay_time) && $ccData->cca_busy_delay_time != '')
				$updatestr .= '{@busy-delay-time="' . $ccData->cca_busy_delay_time . '"}';

		$xml->update($updatestr);

		if (CallCenterManager::updateRealtime())
			self::updateRunning($ccData);
	}

	public static function delete($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$locationRec = Doctrine::getTable('Location')->find($ccData->cca_locationid);
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/agents/agent[@bluebox="' . $ccData->cca_id . '"]';
		$xml->setXmlRoot($root);
		$xml->deleteNode();
		if (CallCenterManager::updateRealtime() == 'realtime')
		{
			$agentname = $ccData->cca_loginid . '@' . $ccData->agentLocation->domain;
			self::removeRunning($agentname);
		}
	}

	public static function updateRunning($ccData, $mode = 'OVERWRITE')
	{
		$agentname = $ccData->cca_loginid . '@' . $ccData->agentLocation->domain;
		try {
			self::addRunning($agentname, $ccData->cca_type);
		} catch (callcenterException $e)
		{
			if ($e->getMessage() == '-ERR Agent already exist!')
				if ($mode != 'OVERWRITE')
					throw $e;
			else
				throw $e;
		}
		self::setRunning($agentname, 'max-no-answer', $ccData->cca_max_no_answer);
		self::setRunning($agentname, 'wrap-up-time', $ccData->cca_wrap_up_time);
		self::setRunning($agentname, 'reject-delay-time', $ccData->cca_reject_delay_time);
		self::setRunning($agentname, 'busy-delay-time', $ccData->cca_busy_delay_time);
		return true;
	}

	public static function removeRunning($agentname)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->api('callcenter_config agent del ' . $agentname);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
		self::setRunning($agentname, 'contact', $ccData->cca_contact);
		return true;
	}

	public static function addRunning($agentname, $agenttype)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->api('callcenter_config agent add ' . $agentname . ' ' . $agenttype);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
			throw new callcenterException($responsestr);
	}

	public static function setRunning($agentname, $varname, $varvalue)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->api('callcenter_config agent set ' . $varname . ' ' . $agentname . ' ' . $varvalue);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
	}

	public function getRunningList()
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->api('callcenter_config agent list');
		$responsestr = $responseobj->getBody();
		if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK')
				return array();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
		$agentStatus_arr = explode("\n", $responsestr);
		$agentStatus = array();
		$keyarr = explode('|', $agentStatus_arr[0]);
		foreach ( $agentStatus_arr as $key => $valuestr) {
				if ($key == 0 || $valuestr == '+OK' || $valuestr == '') continue;
				$valuearr = explode('|', $valuestr);
				$agentStatus[$valuearr[0]] = array_combine($keyarr, $valuearr);
				list($login_id, $domain) = explode('@', $agentStatus[$valuearr[0]]['name']);
				$agentStatus[$valuearr[0]]['login_id'] = $login_id;
				$agentStatus[$valuearr[0]]['domain'] = $domain;
		}
		return $agentStatus;
	}
}
