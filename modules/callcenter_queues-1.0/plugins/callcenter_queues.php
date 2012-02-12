<?php defined('SYSPATH') or die('No direct access allowed.');
class callcenter_queues_Plugin extends Bluebox_Plugin
{
	public function viewcreate_update()
	{
		Event::$data->template->content->locations = Location::dictionary();
		Event::$data->template->content->mohoptions = callcenter_queue::getMOHOptions();
		Event::$data->template->content->tbsoptions = callcenter_queue::getTBSOptions();
	}

	public function subview_tiers()
	{
		$grid = jgrid::grid('callcenter_tier', 
			array(
				'caption' => ''
			)
		);
		
		$grid->where('callcenter_tier/cct_ccq_id', '=', Event::$data->callcenter_queue->ccq_id);
		$grid->add('callcenter_tier/cct_id', 'ID', 
			array(
				'hidden' => true,
				'key' => true
			)
		);

		$grid->add('callcenter_agent/cca_loginid', 'Agent Login');
		$grid->add('callcenter_agent/cca_displayname', 'Agent Name');
		$grid->add('callcenter_tier/cct_level', 'Level');
		$grid->add('callcenter_tier/cct_position', 'Position');

		$grid->addAction('callcenter_tier/delete', 'Delete', 
			array(
				'arguments' => 'callcenter_tier/cct_id'
			)
		);

		$subview = new View('callcenter_queues/tiers');
		$subview->tiergrid = $grid->produce();        
		$this->views[] = $subview;
	}
}

?>