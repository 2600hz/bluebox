<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmhangup_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'getActionLink' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to Hang Up'
			)
		),
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to Hang Up'
			)
		)
	);

	public static function getActionLink($params, $linktype = 'image')
	{
		self::checkRequiredParams('getActionLink', $params);
		self::assignEmptyParams($params, 1);
		list($uuid) = $params;
		$linkattr = array(
			'name' => 'hangup',
			'id' => 'hangup_' . $uuid,
			'class' => 'small_red_button action_button qtipAjaxForm',
			'href' => url::base() . 'index.php/callmanager/hangup/' . $uuid,
			'image' => url::base() . 'modules/callmanager-1.0/assets/img/hangup.png',
			'text' => 'Hangup'
		);
		return parent::getActionLink($linkattr, $linktype);
	}

	public function processAction(&$controller, $params)
	{
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;

		$input = Input::instance();
		if (is_array($input->post('submit')))
		{
			$controller->template->content = new View('callmanager/commandresponse');
			
			if ($input->post('uuid') == '' || key($input->post('submit')) == 'deny')
			{
				$controller->exitQtipAjaxForm();
				exit();
			}
			try {
				$commandresponse = trim($this->executeAction(array($input->post('uuid'))));
				$controller->view->commandresponse = 'The call was successfully hung up.';
			} catch (ESLException $e) {
				$controller->view->commandresponse = '<div class="error">An error has occured: ' . $e->getMessage() . '</div>';
			} catch (callmanagerException $e) {
				$controller->view->commandresponse = '<div class="error">An error has occured: ' . $e->getMessage() . '</div>';
			}
		}
		else
		{
			$controller->template->content = new View('callmanager/hangup');
			$controller->view->uuid = $uuid;
		}
	}

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;
		$eslCon = freeswitch_callmanager_Driver::getESL();

		try {
			$responsestr = trim($eslCon->sendRecv('api uuid_kill ' . $uuid)->getBody());
		} catch (ESLException $e) {
			if (strpos($e->getMessage(), 'Not connected'))
			{
				echo '<div class="error">Unable to connect to the switch console.  This usually means that mod_event_socket is not running, or that the password that you entered during the setup process has changed. The error message was:<br>' . $e->getMessage() .'</span>';
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