<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmvaletpark_Driver extends callmanagerfunction_Driver
{
	public static function getActionLink($params, $linktype = 'image')
	{
		self::checkRequiredParams('getActionLink', $params);
		self::assignEmptyParams($params, 1);
		list($uuid) = $params;
		$linkattr = array(
			'name' => 'park',
			'id' => 'park_' . $uuid,
			'class' => 'button_blue action_button qtipAjaxForm',
			'href' => url::base() . 'index.php/callmanager/valetpark/' . $uuid,
			'image' => url::base() . 'modules/callmanager-1.0/assets/img/valetpark.png',
			'text' => 'Park'
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
			if ($input->post('uuid') == '' || current($input->post('submit')) == 'cancel')
			{
				$controller->exitQtipAjaxForm();
				exit();
			}
			$controller->template->content = new View('callmanager/commandresponse');
			try {
				$destobj = Doctrine::getTable('Number')->findOneByforeign_idAndclass_type($input->post('destlot'), 'ValetParkingLotNumber');
				$commandresponse = $this->executeAction(array($input->post('uuid'), $destobj->number));
				$controller->view->commandresponse = 'Call successfully transfered';
			} catch (ESLException $e) {
				$controller->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
			} catch (callmanagerException $e) {
				$controller->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
			}
		}
		else
		{
			$controller->template->content = new View('callmanager/valetpark');
			$controller->view->uuid = $uuid;
		}
	}

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 5);
		list($uuid, $destination, $parties, $dialplan, $context) = $params;
		$eslCon = freeswitch_callmanager_Driver::getESL();

		try {
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