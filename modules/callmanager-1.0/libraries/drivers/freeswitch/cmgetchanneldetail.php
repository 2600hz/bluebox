<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmgetchanneldetail_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'processAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to getChannelDetail'
			)
		)
	);

	public function processAction(&$controller, $params)
	{
		$this->checkRequiredParams('processAction', $params);
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;
		$callManagerObj = new callManager();
		$channelDetail_view = new View('callmanager/channeldetail');
		$channelDetail_view->detailfields = freeswitch_callmanager_Driver::getDetailFields();
		try {
			$channelDetail_view->channeldata = $callManagerObj->getChannelInfo($uuid, true);
			if ($channelDetail_view->channeldata)
				foreach (freeswitch_callmanager_Driver::getDetailDatetimeFields() as $fieldname)
				{
					if (isset($channelDetail_view->channeldata[$fieldname]) && !$channelDetail_view->channeldata[$fieldname] == 0)
						$channelDetail_view->channeldata[$fieldname] = date('r', $channelDetail_view->channeldata[$fieldname]);
					else
						$channelDetail_view->channeldata[$fieldname] = '';
				}
			$channelDetail_view->render(TRUE);
		} catch (ESL_Exception $e) {
			echo '<div class="error">An error has occured: ' . $e->getMessage() . '<br>';
			if (strpos($e->getMessage(), 'Not connected'))
				echo 'This indicates that Freeswitch is not running, mod_event_socket is not configured, or the system is unable to log in.';
			echo '</div>';
		} catch (callmanagerException $e) {
			$controller->template->content = new View('callmanager/commandresponse');
			echo 'An error has occured: ' . $e->getMessage();
		}
		exit();
	}
}
?>