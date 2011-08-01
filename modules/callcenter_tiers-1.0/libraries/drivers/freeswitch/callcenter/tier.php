<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_callcenter_tier_Driver extends FreeSwitch_Base_Driver
{
	protected static $anInstance = null;

	public static function getInstance()
	{
		if (self::$anInstance == null)
			self::$anInstance = new FreeSwitch_callcenter_tier_Driver();
		return self::$anInstance;
	}

	public static function set($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/tiers';
		$xml->setXmlRoot($root);

		$agentRec = Doctrine::getTable('callcenter_agent')->find($ccData->cct_cca_id);
		$queueRec = Doctrine::getTable('callcenter_queue')->find($ccData->cct_ccq_id);
		$locationRec = Doctrine::getTable('Location')->find($agentRec->cca_locationid);

		$updatestr = '/tier[@agent="' . $agentRec->cca_loginid . '\@' . $locationRec->domain . '"][@queue="' . $queueRec->ccq_name . '\@' . $locationRec->domain . '"]{@level="' . $ccData['cct_level'] . '"}{@position="' . $ccData['cct_position'] . '"}';
		$xml->update($updatestr);
		if (CallCenterManager::updateRealtime())
			self::updateRunning($ccData);
	}

	public static function delete($ccData)
	{
		$xml = Telephony::getDriver()->xml;

		$agentRec = Doctrine::getTable('callcenter_agent')->find($ccData->cct_cca_id);
		$queueRec = Doctrine::getTable('callcenter_queue')->find($ccData->cct_ccq_id);
		$locationRec = Doctrine::getTable('Location')->find($agentRec->cca_location_id);

		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/tiers/tier[@agent="' . $agentRec->cca_loginid . '\@' . $locationRec->domain . '"][@queue="' . $queueRec->ccq_name . '\@' . $locationRec->domain . '"]';
		$xml->setXmlRoot($root);
		$xml->deleteNode();
		if (CallCenterManager::updateRealtime())
			self::removeRunning($ccData);
	}

	public static function updateRunning($ccData, $mode = 'OVERWRITE')
	{
		$queuename = $ccData->callcenter_queue->ccq_name . '@' . $ccData->callcenter_queue->queueLocation->domain;
		$agentname = $ccData->callcenter_agent->cca_loginid . '@' . $ccData->callcenter_agent->agentLocation->domain;
		try {
			self::addRunning($queuename, $agentname, $ccData->cct_level, $ccData->cct_position);
		} catch (callcenterException $e)
		{
			if ($e->getMessage() == '-ERR Agent already exist!')
				if ($mode != 'OVERWRITE')
					throw $e;
			else
				throw $e;
		}
		self::setRunning($queuename, $agentname, 'level', $ccData->cct_level);
		self::setRunning($queuename, $agentname, 'position', $ccData->cct_position);
		return true;
	}

	public static function removeRunning($ccData)
	{
		$eslCon = EslManager::getInstance();
		$queuename = $ccData->callcenter_queue->ccq_name . '@' . $ccData->callcenter_queue->queueLocation->domain;
		$agentname = $agentObj->cca_loginid . '@' . $agentObj->agentLocation->domain;
		$responseobj = $eslCon->sendRecv('api callcenter_config tier del ' . $queuename . ' ' . $agentname);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
	}

	public static function addRunning($queuename, $agentname, $level, $position)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config tier add ' . $queuename . ' ' . $agentname . ' ' . $level . ' ' . $position);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
			throw new callcenterException($responsestr);
	}

	public static function setRunning($queuename, $agentname, $varname, $varvalue)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config tier set ' . $varname . ' ' . $queuename . ' ' . $agentname . ' ' . $varvalue);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
	}

	public function getRunningListByQueue($queuename)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config tier list ' . $queuename);
		$responsestr = $responseobj->getBody();
		if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK')
				return array();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
		$tierList_arr = explode("\n", $responsestr);
		$tierList = array();
		$keyarr = explode('|', $tierList_arr[0]);
		foreach ( $tierList_arr as $key => $valuestr) {
				if ($key == 0 || $valuestr == '+OK' || $valuestr == '') continue;
				$valuearr = explode('|', $valuestr);
				$tierList[$valuearr[0]] = array_combine($keyarr, $valuearr);
				list($queue_name, $queue_domain) = explode('@', $tierList[$valuearr[0]]['queue']);
				$tierList[$valuearr[0]]['queue_name'] = $queue_name;
				$tierList[$valuearr[0]]['queue_domain'] = $queue_domain;
				list($agent_loginid, $agent_domain) = explode('@', $tierList[$valuearr[0]]['agent']);
				$tierList[$valuearr[0]]['agent_loginid'] = $agent_loginid;
				$tierList[$valuearr[0]]['agent_domain'] = $agent_domain;
		}
		return $tierList;
	}
}
