<?php defined('SYSPATH') or die('No direct access allowed.');

class Maintenance_Controller extends Bluebox_Controller
{
    protected $baseModel = 'MaintenanceEvent';

    protected $authBypass = array('index');

    public function index()
    {
        Maintenance::trigger();
        
        die();
    }
}