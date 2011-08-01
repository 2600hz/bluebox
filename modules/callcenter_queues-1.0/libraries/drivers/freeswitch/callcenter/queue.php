<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_callcenter_queue_Driver extends FreeSwitch_Base_Driver
{
	protected static $anInstance = null;

	public static function getInstance()
	{
		if (self::$anInstance == null)
			self::$anInstance = new FreeSwitch_callcenter_queue_Driver();
		return self::$anInstance;
	}

	public static function set($ccData)
	{
		$locationRec = Doctrine::getTable('Location')->find($ccData->ccq_locationid);

		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/queues/queue[@name="' . $ccData->ccq_name . '\@' . $locationRec->domain . '"]';
		$xml->setXmlRoot($root);

		$xml->update('/param[@name="strategy"]{@value="' . $ccData->ccq_strategy . '"}');
		switch ($ccData->ccq_moh_type) {
			case 'local_stream://':
			case 'phrase:':
			case 'file://':
				$xml->update('/param[@name="moh-sound"]{@value="' . str_replace('/', '\/', $ccData->ccq_moh_type) . str_replace('/', '\/', $ccData->ccq_moh_data) . '"}');
				break;
			default:
				$xml->update('/param[@name="moh-sound"]{@value="' . str_replace('/', '\/', $ccData->ccq_moh_type) . '"}');
				break;
		}
        if (!isset($ccData->ccq_record_template) || empty($ccData->ccq_record_template) || $ccData->ccq_record_template == '')
                $xml->deleteNode('/param[@name="record-template"]');
        else
                $xml->update('/param[@name="record-template"]{@value="' . $ccData->ccq_record_template . '"}');

        if (!isset($ccData->ccq_time_based_score) || empty($ccData->ccq_time_based_score) || $ccData->ccq_time_based_score == '')
                $xml->deleteNode('/param[@name="time-base-score"]');
        else
                $xml->update('/param[@name="time-base-score"]{@value="' . $ccData->ccq_time_based_score . '"}');

        if (!isset($ccData->ccq_tier_rule_apply) || empty($ccData->ccq_tier_rule_apply) || $ccData->ccq_tier_rule_apply == '')
                $xml->deleteNode('/param[@name="tier-rules-apply"]');
        else
                $xml->update('/param[@name="tier-rules-apply"]{@value="' . $ccData->ccq_tier_rule_apply . '"}');

        if (!isset($ccData->ccq_tier_rule_wait_time) || empty($ccData->ccq_tier_rule_wait_time) || $ccData->ccq_tier_rule_wait_time == '')
                $xml->deleteNode('/param[@name="tier-rule-wait-second"]');
        else
                $xml->update('/param[@name="tier-rule-wait-second"]{@value="' . $ccData->ccq_tier_rule_wait_time . '"}');

        if (!isset($ccData->ccq_tier_rule_wait_multipy_level) || empty($ccData->ccq_tier_rule_wait_multipy_level) || $ccData->ccq_tier_rule_wait_multipy_level == '')
                $xml->deleteNode('/param[@name="tier-rule-wait-multiply-level"]');
        else
                $xml->update('/param[@name="tier-rule-wait-multiply-level"]{@value="' . $ccData->ccq_tier_rule_wait_multipy_level . '"}');

        if (!isset($ccData->ccq_tier_rule_noagent_nowait) || empty($ccData->ccq_tier_rule_noagent_nowait) || $ccData->ccq_tier_rule_noagent_nowait == '')
                $xml->deleteNode('/param[@name="tier-rule-no-agent-no-wait"]');
        else
                $xml->update('/param[@name="tier-rule-no-agent-no-wait"]{@value="' . $ccData->ccq_tier_rule_noagent_nowait . '"}');

        if (!isset($ccData->ccq_discard_abandonded_after) || empty($ccData->ccq_discard_abandonded_after) || $ccData->ccq_discard_abandonded_after == '')
                $xml->deleteNode('/param[@name="discard-abandoned-after"]');
        else
                $xml->update('/param[@name="discard-abandoned-after"]{@value="' . $ccData->ccq_discard_abandonded_after . '"}');

        if (!isset($ccData->ccq_abandoned_resume_allowed) || empty($ccData->ccq_abandoned_resume_allowed) || $ccData->ccq_abandoned_resume_allowed == '')
                $xml->deleteNode('/param[@name="abandoned-resume-allowed"]');
        else
                $xml->update('/param[@name="abandoned-resume-allowed"]{@value="' . $ccData->ccq_abandoned_resume_allowed . '"}');

        if (!isset($ccData->ccq_max_wait_time) || empty($ccData->ccq_max_wait_time) || $ccData->ccq_max_wait_time == '')
                $xml->deleteNode('/param[@name="max-wait-time"]');
        else
                $xml->update('/param[@name="max-wait-time"]{@value="' . $ccData->ccq_max_wait_time . '"}');

        if (!isset($ccData->ccq_max_wait_time_no_agent) || empty($ccData->ccq_max_wait_time_no_agent) || $ccData->ccq_max_wait_time_no_agent == '')
                $xml->deleteNode('/param[@name="max-wait-time-with-no-agent"]');
        else
                $xml->update('/param[@name="max-wait-time-with-no-agent"]{@value="' . $ccData->ccq_max_wait_time_no_agent . '"}');

        if (!isset($ccData->ccq_max_wait_time_with_no_agent_time_reached) || empty($ccData->ccq_max_wait_time_with_no_agent_time_reached) || $ccData->ccq_max_wait_time_with_no_agent_time_reached == '')
                $xml->deleteNode('/param[@name="max-wait-time-with-no-agent-time-reached"]');
        else
                $xml->update('/param[@name="max-wait-time-with-no-agent-time-reached"]{@value="' . $ccData->ccq_max_wait_time_with_no_agent_time_reached . '"}');

		if (CallCenterManager::updateRealtime())
		{
			$queuename = $ccData->ccq_name . '@' . $ccData->queueLocation->domain;
			self::updateRunning($queuename);
		}
	}

	public static function delete($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$locationRec = Doctrine::getTable('Location')->find($ccData->ccq_locationid);
		$root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]/queues/queue[@name="' . $ccData->ccq_name . '\@' . $locationRec->domain . '"]';
		$xml->setXmlRoot($root);
		$xml->deleteNode();

		if (CallCenterManager::updateRealtime())
		{
			$queuename = $ccData->ccq_name . '@' . $ccData->queueLocation->domain;
			self::removeRunning($queuename);
		}
	}

	public static function dialplan($number)
	{
		$xml = Telephony::getDriver()->xml;

		$destination = $number['Destination'];
		$locationRec = Doctrine::getTable('Location')->find($destination["ccq_locationid"]);

		$xml->update('/action[@application="callcenter"]{@data="' . $destination['ccq_name'] . '\@' . $locationRec->domain . '"}');
	}

	public static function updateRunning($queuename, $mode = 'OVERWRITE')
	{
		self::addRunning($queuename);
		self::setRunning($queuename);
		return true;
	}

	public static function removeRunning($queuename)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config queue unload ' . $queuename);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
	}

	public static function addRunning($queuename)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config queue load ' . $queuename);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
			throw new callcenterException($responsestr);
	}

	public static function setRunning($queuename)
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config queue reload ' . $queuename);
		$responsestr = $responseobj->getBody();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
			throw new callcenterException($responsestr);
	}

	public function getRunningList()
	{
		$eslCon = EslManager::getInstance();
		$responseobj = $eslCon->sendRecv('api callcenter_config queue list');
		$responsestr = $responseobj->getBody();
		if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK')
				return array();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				throw new callcenterException($responsestr);
		$queueStatus_arr = explode("\n", $responsestr);
		$queueStatus = array();
		$keyarr = explode('|', $queueStatus_arr[0]);
		foreach ( $queueStatus_arr as $key => $valuestr) {
				if ($key == 0 || $valuestr == '+OK' || $valuestr == '') continue;
				$valuearr = explode('|', $valuestr);
				$queueStatus[$valuearr[0]] = array_combine($keyarr, $valuearr);
				list($name, $domain) = explode('@', $queueStatus[$valuearr[0]]['name']);
				$queueStatus[$valuearr[0]]['queuename'] = $name;
				$queueStatus[$valuearr[0]]['queuedomain'] = $domain;
		}
		return $queueStatus;
	}
}
