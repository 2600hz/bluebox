<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_tiers_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'callcenter_tier';

    /**
     * Typically we create a grid, but you can define any entry point you
     * would like...
     */
    public function index()
    {
		$this->template->content = new View('generic/grid');
		// Setup the base grid object
		$this->grid = jgrid::grid($this->baseModel, array(
				'caption' => 'Tiers'
			)
		);
		// Add the base model columns to the grid
		$this->grid->add('cct_id', 'ID', array(
				'hidden' => true,
				'key' => true
			)
		);
		$this->grid->add('callcenter_agent/cca_loginid', 'Agent Login');
		$this->grid->add('callcenter_agent/cca_displayname', 'Agent Name');
			$this->grid->add('callcenter_queue/ccq_name', 'Queue Name');
			$this->grid->add('callcenter_agent/cca_locationid', 'Location', array(
			'width' => '60',
			'callback' => array(
				'function' => array($this, 'getlocationname'),
			),
			'search' => false,
			'sortable' => false
		));
        // Add the actions to the grid
        $this->grid->addAction('callcenter_tiers/edit', 'Edit', array(
                'arguments' => 'cct_id'
            )
        );
        $this->grid->addAction('callcenter_tiers/delete', 'Delete', array(
                'arguments' => 'cct_id'
            )
        );

        plugins::views($this);
        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function getlocationname($locationid)
    {
		$locationRec = Doctrine::getTable('location')->find($locationid);
			return $locationRec->domain;
		}

    public function updateagentdropdownbylocation($locid = null)
    {
		if ($locid == null || $locid == 0)
			echo '<option value="">Select a Location First...</option';
		else
		{
			$agentlist = callcenter_agent::getagentsbylocation($locid);
			if (count($agentlist) < 1) echo '<option value="">No agents found at location...</option>';
			else
			{
				foreach ($agentlist as $agent)
				{
					echo '<option value="' . $agent['cca_id'] . '">' . $agent['cca_loginid'] . ' - ' . $agent['cca_displayname'] . '</option>';
				}
			}
		}
		exit();
    }

    public function updatequeuedropdownbylocation($locid = null)
    {
		if ($locid == null || $locid == 0)
			echo '<option value="">Select a Location First...</option';
		else
		{
			$queuelist = callcenter_queue::getqueuesbylocation($locid);
			if (count($queuelist) < 1) echo '<option value="">No queues found at location...</option>';
			else
			{
				foreach ($queuelist as $queue)
				{
					echo '<option value="' . $queue['ccq_id'] . '">' . $queue['ccq_name'] . '</option>';
				}
			}
		}
		exit();
    }
}