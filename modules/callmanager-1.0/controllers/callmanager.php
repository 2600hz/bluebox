<?php defined('SYSPATH') or die('No direct access allowed.');

class callmanager_controller extends Bluebox_Controller
{
	public function __construct()
	{
		parent::__construct();
		stylesheet::add('callmanager', 50);
	}

	public function index()
	{
		$this->template->content = new View('callmanager/index');
		$callmanagerdriverName = strtolower(Telephony::getDriverName()) . '_callmanager_Driver';
		
		$callmanagerdriverObj = new $callmanagerdriverName();
		$this->view->summaryfields = $callmanagerdriverObj->getSummaryFields();
	}
	
	public function __call($funcname, $arguments)
	{
		return callManager::processAction($funcname, $this, $arguments);
	}
}
?>