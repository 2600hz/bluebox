<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmmonitor_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to monitor'
			)
		),
			array(
				'required' => true,
				'message' => 'Required parameter dialstring not passed to monitor'
			)
	);

	public static function getActionLink($params, $linktype = 'image')
	{
		self::checkRequiredParams('getActionLink', $params);
		self::assignEmptyParams($params, 1);
		list($uuid) = $params;
		$linkattr = array(
			'name' => 'callmonitor',
			'id' => 'callmonitor_' . $uuid,
			'class' => 'button_blue action_button qtipAjaxForm',
			'href' => url::base() . 'index.php/callmanager/monitor/' . $uuid,
			'image' => url::base() . 'modules/callmanager-1.0/assets/img/monitor.png',
			'text' => 'Monitor'
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
				$numberobj = doctrine::getTable('Number')->find($input->post('destext'));
				$numbertypeobj = doctrine::getTable($numberobj->class_type)->find($numberobj->foreign_id);
				$destdial = 'user/' . $numbertypeobj->Destination["plugins"]["sip"]["username"] . '@' . $numbertypeobj->Destination->User->Location->domain;
				$commandresponse = $this->executeAction(array($input->post('uuid'), $destdial));
				$controller->view->commandresponse = '<p>Your phone should now ring.  Once you pick it up, you will be monitoring the call and neither party will be able to hear you.</p><br>';
				$controller->view->commandresponse .= '<p>During your monitoring session, you may dial the following numbers on your phone:';
				$controller->view->commandresponse .= '<ul class="clean_indented">';
				$controller->view->commandresponse .= '<li>1 - Speak to the person at the far end of the channel without the person on the channel hearing you</li>';
				$controller->view->commandresponse .= '<li>2 - Speak to the person on the channel without the person at the far end of the channel hearing you</li>';
				$controller->view->commandresponse .= '<li>3 - Bridge into the call and participate in a 3 way conference</li>';
				$controller->view->commandresponse .= '<li>0 - Restore the monitoring session after using one of the other functions</li>';
				$controller->view->commandresponse .= '</ul></p>';
			} catch (ESLException $e) {
				$controller->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
				if (strpos($e->getMessage(), 'Not connected'))
					$controller->view->commandresponse = 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
			} catch (callmanagerException $e) {
				$controller->template->content = new View('callmanager/commandresponse');
				$controller->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
			}
		}
		else
		{
			$controller->template->content = new View('callmanager/monitor');
			$controller->view->uuid = $uuid;
			$controller->view->userext = '1234';
		}
	}

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 2);
		list($uuid, $destdialstr) = $params;
		$cmoriginatedriverobj = new freeswitch_cmoriginate_Driver();
		return $cmoriginatedriverobj->executeAction(array($destdialstr, null, '&eavesdrop(' . $uuid . ')', false));
	}
}


?>