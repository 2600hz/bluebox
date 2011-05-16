<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmstoprecord_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to stopRecord'
			)
		)
	);

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;
		$eslCon = freeswitch_callmanager_Driver::getESL();
		try {
			$getchannelvardriver = new freeswitch_cmgetchannelvar_Driver();
			$responsestr = $eslCon->sendRecv('api uuid_record ' . $uuid . ' stop ' . $getchannelvardriver->executeAction(array($uuid, 'recording_file')))->getBody();
			$eslCon->send('api uuid_setvar ' . $uuid . ' recording_file');
		} catch (ESLException $e) {
			if (strpos($e->getMessage(), 'Not connected'))
			{
				echo '<div class="error">Unable to connect to the switch console.  This usually means that mod_event_socket is not running, or that the password that you entered during the setup process has changed. The error message was:<br>' . $e->getMessage() .'</div>';
				exit();
			}
			else
				throw $e;
		}
		
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				$this->_throwError($responsestr);
				
		return $responsestr;
	}
}


?>