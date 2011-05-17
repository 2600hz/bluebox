<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_core_Controller extends Bluebox_Controller
{
	protected $baseModel = 'callcenter_settings';

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('callcenter_core', 50);
    }

	function index()
	{
		url::redirect(Router_Core::$controller . '/edit/1');
	}

	function syncRunningConfig($step = null)
	{
		$input = Input::instance();
		if (is_array($input->post('submit')))
			$this->exitQtipAjaxForm();
		elseif ($step == null)
		{
			$this->template->content = new View('callcenter_core/syncRunningConfig');
		}
		else
		{
			$ccm = new CallCenterManager();
			$ccm->syncRunningConfig($step);
			exit();
		}
	}
	
	function reload()
	{
		$input = Input::instance();
		if (is_array($input->post('submit')))
		{
			$this->exitQtipAjaxForm();
			exit();	
		}
		$ccm = new CallCenterManager();
		$this->template->content = new View('callcenter_core/commandresponse');
		try {
			$this->view->commandresponse = $ccm->reload();
        } catch (ESLException $e) {
        	$this->view->commandresponse = '<div class="error">An Error has occured: ' . $e->getMessage() . '</div>';
        }
	}
}