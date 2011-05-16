<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmtransfer_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
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
			'name' => 'transfer',
			'id' => 'transfer_' . $uuid,
			'class' => 'small_green_button action_button qtipAjaxForm',
			'href' => url::base() . 'index.php/callmanager/transfer/' . $uuid,
			'image' => url::base() . 'modules/callmanager-1.0/assets/img/transfer.png',
			'text' => 'Transfer'
		);
		return parent::getActionLink($linkattr, $linktype);
	}

	public function processAction(&$controller, $params)
	{
		$this->assignEmptyParams($params, 2);
		list($uuid, $classType) = $params;
		$input = Input::instance();
		if (is_array($input->post('submit')))
		{
			if ($input->post('uuid') == '' || current($input->post('submit')) == 'cancel')
			{
				$controller->exitQtipAjaxForm();
				exit();
			}
			$controller->template->content = new View('callmanager/commandresponse');
			try {
				$destobj = Doctrine::getTable('Number')->findOneByforeign_idAndclass_type($input->post('destlot'), $classType);
				$commandresponse = $this->executeAction(array($input->post('uuid'), $destobj->number));
				$controller->view->commandresponse = 'Call successfully transfered';
			} catch (ESLException $e) {
				$controller->view->commandresponse = '<div class="error">An error has occured: ' . $e->getMessage() . '</div>';
			} catch (callmanagerException $e) {
				$controller->view->commandresponse = '<div class="error">An error has occured: ' . $e->getMessage() . '</div>';
			}
		}
		else
		{
			$controller->template->content = new View('callmanager/transfer');
			$controller->view->classType = $classType;
			$controller->view->uuid = $uuid;
			$currentuser = users::getCurrentUser();
			$userobj = Doctrine::getTable('user')->find($currentuser['user_id']);
			if (isset($userobj->Device[0]->Number[0]))
				$controller->view->userext = $userobj->Device[0]->Number[0]->number;
			else
				$controller->view->userext = null;
		}
	}

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 5);
		list($uuid, $destination, $parties, $dialplan, $context) = $params;
		$eslCon = freeswitch_callmanager_Driver::getESL();

		try {
			$eslCon->send('api uuid_setvar ' . $uuid . ' hangup_after_bridge false');
			$responsestr =  $eslCon->sendRecv('api uuid_transfer ' . $uuid . ($parties ? ' -' . $parties : '') . ' ' . $destination)->getBody();
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