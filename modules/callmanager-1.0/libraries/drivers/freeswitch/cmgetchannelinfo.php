<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmgetchannelinfo_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to getChannelInfo'
			)
		)
	);

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 2, array(null, false));
		list($uuid, $details) = $params;
		$eslCon = freeswitch_callmanager_Driver::getESL();
		
		try {
			if (!$details)
				$responsestr = $eslCon->sendRecv('api show channels like ' . $uuid . ' as xml')->getBody();
			else 
				$responsestr = $eslCon->sendRecv('api uuid_dump ' . $uuid . ' xml')->getBody();
		} catch (ESLException $e) {
			if (strpos($e->getMessage(), 'Not connected'))
			{
				echo '<div class="error">Unable to connect to the switch console.  This usually means that mod_event_socket is not running, or that the password that you entered during the setup process has changed. The error message was:<br>' . $e->getMessage() .'</div>';
				exit();
			}
			else
				throw $e;
		}
		
		if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK' || trim($responsestr) == '<result row_count="0"/>')
				return array();
				
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				$this->_throwError($responsestr);
				
		$xmlarr = xml::xmlstrtoarr($responsestr);
		if (!$xmlarr || count($xmlarr) == 0)
			return array();
		else {
			if (!$details) {
				if (isset($xmlarr['row']['uuid']))
						return array($xmlarr['row']);
				else
						return $xmlarr['row'];
			} else {
				return $xmlarr['headers'];
			}
		}
	}

}


?>