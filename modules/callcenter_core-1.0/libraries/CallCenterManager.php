<?php defined('SYSPATH') or die('No direct access allowed.');

class CallCenterManager {

	protected $eslManager = null;

	protected static $settings = null;

	function __construct() {
			$this->__init_eslManager();
	}

	function __init_eslManager()
	{
		if ($this->eslManager == null) $this->eslManager = new EslManager();
	}

	public static function updateRealtime()
	{
		if (self::$settings == null)
			self::$settings = Doctrine_Query::create()->from('callcenter_settings')->limit(1)->execute();

		if (self::$settings->cc_update_mode = 'realtime')
			return true;

		return false;
	}

	public static function provideNumberTargets()
	{
			$target_objects = array();

			$queues = Doctrine::getTable('callcenter_queue')->findAll(Doctrine::HYDRATE_ARRAY);

			foreach ($queues as $queue)
			{
			$locationRec = Doctrine::getTable('Location')->find($queue['ccq_locationid']);
			$target_objects[] = array(
					'object_name' => $queue['ccq_name'] . '@' . $locationRec->domain . '(' . $locationRec->name . ')',
					'object_description' => 'Callcenter Queue',
					'object_number_type' => 'callcenter_queueNumber',
					'object_id' =>  $queue['ccq_id'],
			);
			}

			Event::$data['callcenter_queueNumber'] = array(
			'short_name' => 'CCQueue',
			'display_name' => 'Callcenter Queue',
			'target_objects' => $target_objects,
			'quick_add' =>'/callcenter_queues/create'
			);
	}

	function getQueueStatus($queueId) {
			$queueObj = Doctrine::getTable('callcenter_queue')->find($queueId);
			if (!$queueObj)
					throw new callcenterException('Queue Not Found: ' . $queueId);
			$locationObj = Doctrine::getTable('Location')->find($queueObj->ccq_locationid);

			$eslCon = $this->eslManager->getESL();
			$responseobj = $eslCon->sendRecv('api callcenter_config queue list members' . $queueObj->ccq_name . '@' . $locationObj->domain);
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
					$queueStatus[$valuearr[2]] = array_combine($keyarr, $valuearr);
			}
			return $queueStatus;
	}

	function getTierList($queueId)
	{
			$queueObj = Doctrine::getTable('callcenter_queue')->find($queueId);

			if (!$queueObj)
					throw new callcenterException('Queue Not Found: ' . $queueId);

			$eslCon = $this->eslManager->getESL();
			$responseobj = $eslCon->sendRecv('api callcenter_config tier list ' . $queueObj->ccq_name . '@' . $queueObj->queueLocation->domain);
			$responsestr = $responseobj->getBody();
			if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK')
					return array();
			if (substr(trim($responsestr), 0, 4) == '-ERR')
					throw new callcenterException($responsestr);

			$responsearr = explode("\n", $responsestr);
			$keyarr = explode('|', $responsearr[0]);
			$tierList = array();
			foreach ( $responsearr as $key => $valuestr) {
					if ($key == 0 || $valuestr == '+OK' || $valuestr == '') continue;
					$valuearr = explode('|', $valuestr);
					$tierList[$valuearr[1]] = array_combine($keyarr, $valuearr);
					$queuenamedomain = explode('@', $tierList[$valuearr[1]]['queue']);
					$tierList[$valuearr[1]]['queue_name'] = $queuenamedomain[0];
					$tierList[$valuearr[1]]['queue_domain'] = $queuenamedomain[1];
					$agentlogindomain = explode('@', $tierList[$valuearr[1]]['agent']);
					$tierList[$valuearr[1]]['agent_loginid'] = $agentlogindomain[0];
					$tierList[$valuearr[1]]['agent_domain'] = $agentlogindomain[1];
			}
			return $tierList;
	}

	function getQueueAgentStatus($queueId) {
			$queueObj = Doctrine::getTable('callcenter_queue')->find($queueId);
			if (!$queueObj)
					throw new callcenterException('Queue Not Found: ' . $queueId);
			$tierList = $this->getTierList($queueId);

			$eslCon = $this->eslManager->getESL();
			$responseobj = $eslCon->sendRecv('api callcenter_config agent list');
			$responsestr = $responseobj->getBody();
			if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK')
					return array();
			if (substr(trim($responsestr), 0, 4) == '-ERR')
					throw new callcenterException($responsestr);

			$responsearr = explode("\n", $responsestr);
			$keyarr = explode('|', $responsearr[0]);
			$agentStatus = array();
			foreach ( $responsearr as $key => $valuestr) {
					if ($key == 0 || $valuestr == '+OK' || $valuestr == '') continue;
					$valuearr = explode('|', $valuestr);
					if (!array_key_exists($valuearr[0], $tierList))
							continue;

					$agentStatus[$valuearr[0]] = array_combine($keyarr, $valuearr);
					$agentlogindomain = explode('@', $agentStatus[$valuearr[0]]['name']);
					$agentStatus[$valuearr[0]]['loginid'] = $agentlogindomain[0];
					$agentStatus[$valuearr[0]]['domain'] = $agentlogindomain[1];
			}

			return $agentStatus;
	}

	function setAgentStatus($agentid, $status)
	{
		$agentObj = Doctrine::getTable('callcenter_agent')->find($agentid);
		if (!$agentObj)
			throw new callcenterException('Agent Not Found: ' . $agentid);

		$eslCon = $this->eslManager->getESL();
		$responseobj = $eslCon->sendRecv('api callcenter_config agent set status ' . $agentObj->cca_loginid . '@' . $agentObj->agentLocation->domain . ' \'' . $status . '\'');
		return $responseobj->getBody();
	}

	public function getRunningQueueList()
	{
		$driverName = Telephony::getDriverName();
        $queueDriver = call_user_func(array($driverName . '_callcenter_queue_Driver', 'getInstance'));

		$locationobjlist = Doctrine_Query::create()
			->select('*')
			->from('Location')->execute();
		foreach ($locationobjlist as $location)
		{
			$locationdomainlist[] = $location->domain;
		}

		$queuelist = $queueDriver->getRunningList();
		arr::alfilter($queuelist, array('queuedomain' => $locationdomainlist));
		return $queuelist;
	}

	public function getRunningAgentList()
	{
		$driverName = Telephony::getDriverName();
        $agentDriver = call_user_func(array($driverName . '_callcenter_agent_Driver', 'getInstance'));

		$locationobjlist = Doctrine_Query::create()
			->select('*')
			->from('Location')->execute();
		foreach ($locationobjlist as $location)
		{
			$locationdomainlist[] = $location->domain;
		}

		$agentlist = $agentDriver->getRunningList();
		arr::alfilter($agentlist, array('domain' => $locationdomainlist));
		return $agentlist;
	}

	public function syncRunningConfig($step)
	{
		while (ob_get_level()) {
				ob_end_clean();
		}
		$sessionobj = Session::instance();
		if ($step == 'setup')
			try {
				//build domain filter
				$locationobjlist = Doctrine_Query::create()
					->select('*')
					->from('Location')
					->execute();
				foreach ($locationobjlist as $location)
				{
					$locationidlist[] = $location->location_id;
				}
				$sessionobj->set('locationlist', $locationidlist);
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'buildrunningqueuelist'));
			} catch (Exception $e) {
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => ''));
				exit();
			}
		elseif ($step == 'buildrunningqueuelist')
			try {
				$sessionobj->set('runningqueuelist', $this->getRunningQueueList());
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'builddbqueuelist'));
			} catch (Exception $e){
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => 'buildrunningagentlist'));
				exit();
			}
		elseif ($step == 'builddbqueuelist')
			try {
				$queuenamelist = array();
				$dbqueueobjlist = Doctrine_Query::create()
					->select('*')
					->from('callcenter_queue')
					->execute();

				foreach ($dbqueueobjlist as $dbqueueobj)
				{
					$queuenamelist[$dbqueueobj->name . '@' . $dbqueueobj->queueLocation->domain] = $dbqueueobj;
				}
				$sessionobj->set('queuenamelist', $queuenamelist);
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'reconcilequeues'));
			} catch (Exception $e){
				$sessionobj->delete('runningqueuelist');
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => 'buildrunningagentlist'));
				exit();
			}
		elseif ($step == 'reconcilequeues')
		{
			$queuestatus = '';
			$queuenamelist = $sessionobj->get('queuenamelist', array());
			$runningqueuelist = $sessionobj->get('runningqueuelist', array());
			foreach ($runningqueuelist as $runningqueue)
			{
				if (!isset($queuenamelist[$runningqueue['name']]))
					try {
						$this->removeRunningQueue($runningqueue['name']);
					} catch (callcenterException $e) {
						$queuestatus .= $e->getMessage() . "\n";
						kohana::Log('error', $e->getMessage());
					}
			}
			foreach ($queuenamelist as $queuename => $queueobj)
			{
				$this->updateRunningQueue($queuename);
			}
			if ($queuestatus != '')
			{
				$sessionobj->delete('runningqueuelist');
				$sessionobj->delete('queuenamelist');
				echo json_encode(array('result' => 'ERROR', 'message' => "Error Syncronizing Queues:\n" . $queuestatus, 'nextstep' => 'buildrunningagentlist'));
				exit();
			}
			$sessionobj->delete('runningqueuelist');
			$sessionobj->delete('queuenamelist');
			echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'buildrunningagentlist'));
		}
		elseif ($step == 'buildrunningagentlist')
			try {
				$sessionobj->set('runningagentlist', $this->getRunningAgentList());
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'builddbagentlist'));
			} catch (Exception $e){
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => ''));
				exit();
			}
		elseif ($step == 'builddbagentlist')
			try {
				$agentnamelist = array();
				$dbagentobjlist = Doctrine_Query::create()
					->select('*')
					->from('callcenter_agent')
					->execute();

				foreach ($dbagentobjlist as $dbagentobj)
				{
					$agentnamelist[$dbagentobj->cca_loginid . '@' . $dbagentobj->agentLocation->domain] = $dbagentobj;
				}
				$sessionobj->set('agentnamelist', $agentnamelist);
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'reconcileagents'));
			} catch (Exception $e){
				$sessionobj->delete('runningagentlist');
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => ''));
				exit();
			}
		elseif ($step == 'reconcileagents')
			try {
				$agentstatus = '';
				$agentnamelist = $sessionobj->get('agentnamelist', array());
				$runningagentlist = $sessionobj->get('runningagentlist', array());
				foreach ($runningagentlist as $runningagent)
				{
					if (!isset($agentnamelist[$runningagent['name']]))
						try {
							$this->removeRunningAgent($runningagent['name']);
						} catch (callcenterException $e) {
							$agentstatus .= $e->getMessage() . "\n";
							kohana::Log('error', $e->getMessage());
						}
				}
				foreach ($agentnamelist as $agentname => $agentobj)
				{
					$this->updateRunningAgent($agentobj);
				}
				if ($agentstatus != '')
				{
					echo json_encode(array('result' => 'ERROR', 'message' => "Error Syncronizing Agents:\n" . $agentstatus, 'nextstep' => 'syncronizetiers'));
					exit();
				}
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => 'syncronizetiers'));
			} catch (Exception $e){
				$sessionobj->delete('runningagentlist');
				$sessionobj->delete('agentnamelist');
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => 'syncronizetiers'));
				exit();
			}
		elseif ($step == 'syncronizetiers')
			try {
				$dbtierobjlist = Doctrine_Query::create()
					->select('*')
					->from('callcenter_tier')
					->execute();

				foreach ($dbtierobjlist as $tierobj)
				{
					$this->updateRunningTier($tierobj);
				}

				$sessionobj->delete('runningagentlist');
				$sessionobj->delete('agentnamelist');
				echo json_encode(array('result' => 'OK', 'message' => 'Success', 'nextstep' => ''));
			} catch (Exception $e){
				$sessionobj->delete('runningagentlist');
				$sessionobj->delete('agentnamelist');
				kohana::Log('error', $e->getMessage());
				echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage, 'nextstep' => ''));
				exit();
			}
		else
		{
			$sessionobj->delete('runningagentlist');
			$sessionobj->delete('agentnamelist');
			echo json_encode(array('result' => 'ERROR', 'message' => $e->getMessage));
			kohana::Log('error', 'Invalid step passed to syncRunningConfig.');
			exit();
		}
		return true;
	}

	public function updateRunningTier($tierobj)
	{
		$driverName = Telephony::getDriverName();
        $tierDriver = call_user_func(array($driverName . '_callcenter_tier_Driver', 'getInstance'));
		$tierDriver->updateRunning($tierobj);
	}

	public function updateRunningAgent($agentobj)
	{
		$driverName = Telephony::getDriverName();
        $agentDriver = call_user_func(array($driverName . '_callcenter_agent_Driver', 'getInstance'));
		$agentDriver->updateRunning($agentobj);
	}

	public function updateRunningQueue($queuename)
	{
		$driverName = Telephony::getDriverName();
        $queueDriver = call_user_func(array($driverName . '_callcenter_queue_Driver', 'getInstance'));
		$queueDriver->updateRunning($queuename);
	}

	public function removeRunningQueue($queuename)
	{
		$runningtierlist = $this->getRunningTierListByQueue($queuename);
		foreach ($runningtierlist as $tier)
		{
			$queuename = substr($tier['queue'], 0, strpos($tier['queue'], '@') - 1);
			$queuedomain = substr($tier['queue'], strpos($tier['queue'], '@'));
			$agentid = substr($tier['agent'], 0, strpos($tier['agent'], '@') - 1);
			$agentdomain = substr($tier['agent'], strpos($tier['agent'], '@'));

			$agentObj = Doctrine_Query::create()
				->select('ca.*')
				->from('callcenter_agent ca')
				->leftJoin('ca.agentLocation al')
				->where('ca.cca_loginid = ' . $agentid)
				->andWhere('al.domain = ' . $agentdomain)
				->execute();

			$queueObj = Doctrine_Query::create()
				->select('cq.*')
				->from('callcenter_queue cq')
				->leftJoin('cq.queueLocation ql')
				->where('cq.ccq_name = ' . $queuename)
				->andWhere('al.domain = ' . $queuedomain)
				->execute();

			$tierObj = Doctrine_Query::create()
				->select('ct.*')
				->from('callcenter_tier ct')
				->where('ct.cct_cca_id = ' . $agentObj->cca_id)
				->andWhere('ct.cct_ccq_id = ' . $queueObj->ccq_id)
				->execute();
			$this->removeRunningTier($tierObj);
		}

		$driverName = Telephony::getDriverName();
        $queueDriver = call_user_func(array($driverName . '_callcenter_queue_Driver', 'getInstance'));
        $queueDriver->removeRunning($queuename);
	}

	public function getRunningTierListByQueue($queuename)
	{
		$driverName = Telephony::getDriverName();
        $tierDriver = call_user_func(array($driverName . '_callcenter_tier_Driver', 'getInstance'));
        $runningtierlist = $tierDriver->getRunningListByQueue($queuename);
        return $runningtierlist;
	}

	public function removeRunningTier($tier)
	{
		$driverName = Telephony::getDriverName();
        $tierDriver = call_user_func(array($driverName . '_callcenter_tier_Driver', 'getInstance'));
        $tierDriver->removeRunning($tier);
        return true;
	}

	public function removeRunningAgent($agentname)
	{
		$driverName = Telephony::getDriverName();
        $agentDriver = call_user_func(array($driverName . '_callcenter_agent_Driver', 'getInstance'));
        $agentDriver->removeRunning($agentname);
        return true;
	}
	
	public function reload()
	{
		$driverName = Telephony::getDriverName();
        	$result = call_user_func(array($driverName . '_callcenter_settings_Driver', 'reload'));
	}

	public function installFeatures()
    {
		//register features
		try {
			Feature::reregister(
				'ccuuidbridgeext',
				'callcenter_agents',
				'Agent UUID Bridge Dialplan (Agent ID = Extention)',
				'UUID Agent dial in and log in to callcenter.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (featureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'ccuuidbridgeget',
				'callcenter_agents',
				'Agent UUID Bridge Dialplan (Get Agent)',
				'UUID Agent dial in and log in to callcenter.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (featureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'ccagentloginext',
				'callcenter_agents',
				'Agent Log In Dialplan (Agent ID = Extention)',
				'Log in to the Callcenter using the current sip user.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (featureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'ccagentlogoutext',
				'callcenter_agents',
				'Agent Log Out Dialplan (Agent ID = Extention)',
				'Log out of the Callcenter using the current sip user.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (featureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'ccagentloginget',
				'callcenter_agents',
				'Agent Log In Dialplan (Get Agent ID)',
				'Request user them Log in to the Callcenter.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (featureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

    	try {
			Feature::reregister(
				'ccagentlogoutget',
				'callcenter_agents',
				'Agent Log Out Dialplan (Get Agent ID)',
				'Request user then Log out of the Callcenter.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (featureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}
    }
}

?>