<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_agents_Plugin extends Bluebox_Plugin
{
	public function viewcreate_update()
	{
		Event::$data->template->content->locations = Location::dictionary();
		Event::$data->template->content->agent_types = callcenter_agent::getTypes();
		Event::$data->template->content->statuses = callcenter_agent::getStatuses();
	}
	
	public function subview_tiers()
	{
		$grid = jgrid::grid('callcenter_tier', 
			array(
				'caption' => ''
			)
		);
		
		$grid->where('callcenter_tier/cct_cca_id', '=', Event::$data->callcenter_agent->cca_id);
		$grid->add('callcenter_tier/cct_id', 'ID', 
			array(
				'hidden' => true,
				'key' => true
			)
		);
		$grid->add('callcenter_queue/ccq_name', 'Queue Name');
		$grid->add('callcenter_tier/cct_level', 'Level');
		$grid->add('callcenter_tier/cct_position', 'Position');

		$grid->addAction('callcenter_tier/delete', 'Delete', 
			array(
				'arguments' => 'callcenter_tier/cct_id'
			)
		);

		$subview = new View('callcenter_agents/tiers');
		$subview->tiergrid = $grid->produce();        
		$this->views[] = $subview;
	}
}
?>