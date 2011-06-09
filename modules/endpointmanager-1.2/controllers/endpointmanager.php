<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'EndpointDevice';

    public function generate($mac_address)
    {
        $this->auto_render = false;
    }

    function index()
	{
		url::redirect(Router_Core::$controller . '/edit/1');
	}
}

?>