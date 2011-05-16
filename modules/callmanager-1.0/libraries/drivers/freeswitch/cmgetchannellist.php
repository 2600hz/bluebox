<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmgetchannellist_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array();

	public function processAction(&$controller, $params) {
		$this->assignEmptyParams($params, 2, array('true'));
		list($showdetail, $filters) = $params;

		$channellist_view = new View('callmanager/channellist');
		$channellist_view->summaryfields = freeswitch_callmanager_Driver::getSummaryFields();
		$channellist_view->detailfields = freeswitch_callmanager_Driver::getDetailFields();

		$callManagerObj = new callManager();
		$input = Input::instance();
		
		if (!$filters)
		{
			$filters = array();
			$filters['state'] = $input->post('state_filters');
		}

		try {
			$channellist = $this->executeAction(array());
			arr::alfilter($channellist, $filters);
			arr::alsort($channellist, $input->post('channel_order'));
			foreach (array_keys($channellist) as $curkey)
				$channellist[$curkey]['actions'] = callmanagerHelper::getFunctionLinksForCall($channellist[$curkey], $channellist[$curkey]['uuid']);
			$channellist_view->channellist = $channellist;
			$channellist_view->updated = date('r');
			$channellist_view->showdetail = $showdetail;
			$channellist_view->render(TRUE);
		} catch (ESL_Exception $e) {
			echo 'An error has occured: ' . $e->getMessage() . '<br>';
			if (strpos($e->getMessage(), 'Not connected'))
				echo 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
		} catch (callmanagerException $e) {
			echo 'An error has occured: ' . $e->getMessage() . '<br>';
		}
		exit();
	}

	public function executeAction($params)
	{
		$eslCon = freeswitch_callmanager_Driver::getESL();

		try {
			$responsestr = $eslCon->sendRecv('api show channels as xml')->getBody();
		} catch (ESLException $e) {
			if (strpos($e->getMessage(), 'Not connected'))
			{
				echo '<div class="error">Unable to connect to the switch console.  This usually means that mod_event_socket is not running, or that the password that you entered during the setup process has changed. The error message was:<br>' . $e->getMessage() .'</span>';
				exit();
			}
			else
				throw $e;
		}

		if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK' || trim($responsestr) == '<result row_count="0"/>')
				return array();
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				$this->_throwError($responsestr);

		$channelStatus = xml::xmlstrtoarr($responsestr);
		if (count($channelStatus) == 0)
				return $channelStatus;
		else
				if (isset($channelStatus['row']['uuid']))
						return array($channelStatus['row']);
				else
						return $channelStatus['row'];
	}

}


?>