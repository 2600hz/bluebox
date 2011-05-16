<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmoriginate_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter destination not passed to originate'
			)
		)
	);

	public static function getActionLink($params = array())
	{
		return '<a id="originate" name="originate" value="originate" class="button_blue action_button qtipAjaxForm" href="' . url::base() . 'index.php/callmanager/originate"><img src="' . url::base() . 'modules/callmanager-1.0/assets/img/originate.png" title="Originate" alt="Originate"/></a>';
	}

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 4, array('', '', '', true));
		list($dest, $srcext, $options, $waitforresponse) = $params;
		//sofia/default/2001@yourvsp.com
		$cmdstr = 'api originate ' . $dest . ' ';
		if ($srcext != null)
			$cmdstr .= $srcext;
		if ($options != null)
			$cmdstr .= $options;
			
		$eslCon = freeswitch_callmanager_Driver::getESL();
		try {
			if ($waitforresponse)
				$responsestr = $eslCon->sendRecv($cmdstr)->getBody();
			else {
				$eslCon->send($cmdstr);
				$responsestr = '';
			}
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
				return true;
				
		if (substr(trim($responsestr), 0, 4) == '-ERR')
				$this->_throwError($responsestr);
				
		return $responsestr;
	}
}


?>