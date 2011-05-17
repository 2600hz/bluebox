<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_tiers_Plugin extends Bluebox_Plugin
{
	public function viewcreate_update()
	{
		$locations = Location::dictionary();
			
		if (count($locations) > 1)
		{
			$locations = array(''=>'Select a Location...') + $locations;
			Event::$data->template->content->agentlist = array('' => 'Select a Location First...');
			Event::$data->template->content->queuelist = array('' => 'Select a Location First...');
		}
		else
		{
			$locid = key($locations);
			
			$agentlist = callcenter_agent::getagentsbylocation($locid);
			$agentoptions = '';
			if (count($agentlist) < 1) echo '<option value="">No agents found at location...</option>';
			else
			{
				foreach ($agentlist as $key => $agent)
				{
					$agentoptions[$agent['cca_id']] = $agent['cca_loginid'] . ' - ' . $agent['cca_displayname'];
				}
			}
			Event::$data->template->content->agentlist = $agentoptions;
			
			$queuelist = callcenter_queue::getqueuesbylocation($locid);
			$queueoptions = '';
			if (count($queuelist) < 1) echo '<option value="">No queues found at location...</option>';
			else
			{
				foreach ($queuelist as $key => $queue)
				{
					  $queueoptions[$queue['ccq_id']] = $queue['ccq_name'];
				}
			}
			Event::$data->template->content->queuelist = $queueoptions;
		}
		Event::$data->template->content->locations = $locations;

	}

	public function populate_parms()
	{
		if (isset(Router::$arguments[0]) and Router::$arguments[0] !== 'null')
			$this->location = Router::$arguments[0];
		if (isset(Router::$arguments[1]) and Router::$arguments[1] !== 'null')
			$this->callcenter_tier->cct_cca_id = Router::$arguments[1];
		if (isset(Router::$arguments[2]) and Router::$arguments[2] !== 'null')
			$this->callcenter_tier->cct_ccq_id = Router::$arguments[2];
	}
}
?>