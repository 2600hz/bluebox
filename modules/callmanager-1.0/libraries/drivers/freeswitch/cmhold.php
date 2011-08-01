<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmhold_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to hold'
			)
		),
		'getActionLink' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to hold'
			)
		)
	);

	public static function getActionLink($params, $linktype = 'image')
	{
		self::checkRequiredParams('getActionLink', $params);
		self::assignEmptyParams($params, 1);
		list($uuid) = $params;
		$linkattr = array(
			'name' => 'hold',
			'id' => 'hold_' . $uuid,
			'class' => 'button_blue action_button qtipAjaxForm',
			'href' => url::base() . 'index.php/callmanager/hold/' . $uuid,
			'image' => url::base() . 'modules/callmanager-1.0/assets/img/hold.png',
			'text' => 'Hold'
		);
		return parent::getActionLink($linkattr, $linktype);
	}

	public function processAction(&$controller, $params)
	{
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;
		$controller->template->content = new View('callmanager/commandresponse');
		try {
			$input = Input::instance();
			$cmisonholddriverobj = new freeswitch_cmisonhold_Driver();
			if (is_array($input->post('submit')))
			{
				if ($input->post('uuid') == '' || current($input->post('submit')) == 'no')
				{
					$controller->exitQtipAjaxForm();
					exit();
				}
				$this->executeAction(array($input->post('uuid'), $input->post('holdaction')));
				$controller->view->commandresponse = 'The call was successfully ';
				if ($cmisonholddriverobj->executeAction(array($input->post('uuid'))))
					$controller->view->commandresponse .= 'put on hold.';
				else
					$controller->view->commandresponse .= 'taken off of hold.';
			}
			else
			{
				$callmanager_obj = new callManager();
				$controller->template->content = new View('callmanager/hold');
				$controller->view->uuid = $uuid;
				$controller->view->onHold = $cmisonholddriverobj->executeAction(array($uuid));
			}
		} catch (ESLException $e) {
			$controller->view->commandresponse = '<div class="error">An error has occured: ' . $e->getMessage() . '</div>';				
		} catch (callmanagerException $e) {
			$controller->view->commandresponse = '<div class="error">An error has occured: ' . $e->getMessage() . '</div>';
		}
	}

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 2);
		list($uuid, $action) = $params;
		$eslCon = freeswitch_callmanager_Driver::getESL();

		try {
			Kohana::log('debug', 'api uuid_hold ' . ($action == '' ? '' : ($action . ' ')). $uuid);
			$responsestr = $eslCon->sendRecv('api uuid_hold ' . ($action == '' ? '' : ($action . ' ')). $uuid)->getBody();
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