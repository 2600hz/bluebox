<?php defined('SYSPATH') or die('No direct access allowed.');

class CallCenter_Controller extends Bluebox_Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
        stylesheet::add('callcenter');
        javascript::add('callcenter');
        javascript::add('jquery-ui-1-7-2-custom');

        // Prepare device info for use
        $device_info = array();

        $location_id = users::getAttr('location_id');

        $location = Doctrine::getTable('Location')->findOneBy('location_id', $location_id);

        foreach($location['User'] as $user)
        {
            foreach($user['Device'] as $device)
            {   
                $device_info[] = array('name' => $device['name'], 'id' => $device['device_id']);
            }
        }

        $this->view->devices = $device_info;
        $this->view->domain = $location['domain'];
    }

    public function queues($queue_id = NULL)
    {
        CallCenter::_API($queue_id, 'queue_id', 'Queue');
    }

    public function agents($agent_id = NULL)
    {
        CallCenter::_API($agent_id, 'agent_id', 'Agent');
    }

    public function tiers($tier_id = NULL)
    {
        CallCenter::_API($tier_id, 'tier_id', 'Tier', array('POST' => 'CallCenter::tiers_POST($id, $envelope)', 'GET' => 'CallCenter::tiers_GET($id, $envelope)'));
    }
}