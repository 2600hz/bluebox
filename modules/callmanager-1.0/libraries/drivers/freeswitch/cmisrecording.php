<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_cmisrecording_Driver extends callmanagerfunction_Driver
{
	private $requiredparams = array(
		'executeAction' => array(
			array(
				'required' => true,
				'message' => 'Required parameter UUID not passed to isRecording'
			)
		)
	);

	public function executeAction($params)
	{
		$this->checkRequiredParams('executeAction', $params);
		$this->assignEmptyParams($params, 1);
		list($uuid) = $params;
		$cmgetchannelvar_obj = new freeswitch_cmgetchannelvar_Driver();
		$recfile = $cmgetchannelvar_obj->executeAction(array($uuid, 'recording_file'));
		if (trim($recfile) == '_undef_')
			return false;
		else
			return $recfile;
	}
}


?>