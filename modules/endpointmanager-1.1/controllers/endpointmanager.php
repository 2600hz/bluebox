<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'EndpointDevice';

    public function generate($file, $mac_address)
    {
        $this->auto_render = false;
    }
}
