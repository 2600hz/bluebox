<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmrecord_Driver extends callmanagerfunction_Driver
{
	public static function getActionLink($params, $linktype = 'image')
	{
		self::checkRequiredParams('getActionLink', $params);
		self::assignEmptyParams($params, 1);
		list($uuid) = $params;
		$linkattr = array(
			'name' => 'record',
			'id' => 'record_' . $uuid,
			'class' => 'button_blue action_button qtipAjaxForm',
			'href' => url::base() . 'index.php/callmanager/record/' . $uuid,
			'image' => url::base() . 'modules/callmanager-1.0/assets/img/record.png',
			'text' => 'Record'
		);
		return parent::getActionLink($linkattr, $linktype);
	}
	
	public function processAction(&$controller, $params)
	{
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;
		try {
			$input = Input::instance();
			$isrecordingdriverobj = new freeswitch_cmisrecording_Driver();
			if (is_array($input->post('submit')))
			{
				if ($input->post('uuid') == '' || key($input->post('submit')) == 'deny')
				{
					$controller->exitQtipAjaxForm();
					exit();
				}
				$controller->template->content = new View('callmanager/commandresponse');
				$file_name = str_replace(array('%uuid%', '%date%'), array($input->post('uuid'), date('Y-m-d_H:i:s')), $input->post('file_name'));
				$filerecordbase = Kohana::config('freeswitch.audio_root') . '/../recordings/' . $file_name . '.wav';
				$limit = $input->post('max_record_time');
				if ($limit < 10)
					$limit = '';
				$callmanager_obj = new callManager();
				if (current($input->post('submit')) == 'yes')
				{
					$record_file = $isrecordingdriverobj->executeAction(array($uuid));
					$stoprecordingdriverobj = new freeswitch_cmstoprecord_Driver();
					$commandresponse = $stoprecordingdriverobj->executeAction(array($input->post('uuid')));
				}
				else
				{
					$startrecordingdriverobj = new freeswitch_cmstartrecord_Driver();
					$commandresponse = $startrecordingdriverobj->executeAction(array($input->post('uuid'), $filerecordbase, $limit));
				}
				if (substr(trim($commandresponse), 0, 3) == '+OK')
				{
					$controller->view->commandresponse = 'Recording has ';
					if (current($input->post('submit')) == 'yes')
						$controller->view->commandresponse .= 'been stopped. The recording is available as<br><span style="font-weight: bold;"><a href="' . url::site('/callrecord/get/' . urlencode(substr(basename($record_file), 0, -4))) . '">'. basename($record_file) . '</a></span><br>in the recording folder.<br><audio src="' . url::site('/callrecord/listen/' . urlencode(substr(basename($record_file), 0, -4))) . '" type="audio/wav" controls="controls">No audio tag support</audio>';
					else
						$controller->view->commandresponse .= 'started as <br><span style="font-weight: bold;">' . $file_name . '.wav</span>';
				}
				else
					$controller->view->commandresponse = 'An Error Has Occurred: <br>' . $controller->view->commandresponse;
			}
			else
			{
				$callmanager_obj = new callManager();
				$controller->template->content = new View('callmanager/record');
				$controller->view->uuid = $uuid;
				//find out if the call is currently being recorded
				$controller->view->record_file = basename($isrecordingdriverobj->executeAction(array($uuid)));
				$controller->view->file_name = '%uuid%_%date%';
			}
		} catch (ESLException $e) {
			$controller->template->content = new View('callmanager/commandresponse');
			$controller->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
			if (strpos($e->getMessage(), 'Not connected'))
				$controller->view->commandresponse = 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
		} catch (callmanagerException $e) {
			$controller->template->content = new View('callmanager/commandresponse');
			$controller->view->commandresponse = 'An error has occured: ' . $e->getMessage() . '<br>';
		}
	}
}


?>