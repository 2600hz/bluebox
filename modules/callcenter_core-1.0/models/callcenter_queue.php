<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_queue extends Bluebox_Record
{
	public static $errors = array(
		'ccq_name' => array(
			'notnull' => 'You must enter a name.',
			'notblank' => 'You must enter a name.',
			'queuedup' => 'This Queue already exists in this location.'
		),
		'ccq_locationid' => array(
			'notnull' => 'You must select a locatation.',
			'notblank' => 'You must select a location.',
			'queuedup' => 'This Queue already exists in this location.'
		)
	);


	function setTableDefinition()
	{
		$this->hasColumn('ccq_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('ccq_name', 'string', 200, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('ccq_locationid', 'integer', 11, array('unsigned' => true));
		$this->hasColumn('ccq_strategy', 'string', 50, array('notnull' => true, 'notblank' => true, 'default' => 'agent-with-least-talk-time'));
		$this->hasColumn('ccq_moh_type', 'string', 20);
		$this->hasColumn('ccq_moh_data', 'string');
		$this->hasColumn('ccq_record_template', 'string');
		$this->hasColumn('ccq_time_based_score', 'string', 10, array('default' => 'queue'));
		$this->hasColumn('ccq_tier_rule_apply', 'string', 5, array('default' => 'false'));
		$this->hasColumn('ccq_tier_rule_wait_time', 'integer', 11, array('unsigned' => true, 'default' => 0));
		$this->hasColumn('ccq_tier_rule_wait_multipy_level', 'string', 5, array('default' => 'true'));
		$this->hasColumn('ccq_tier_rule_noagent_nowait', 'string', 5, array('default' => 'true'));
		$this->hasColumn('ccq_discard_abandonded_after', 'integer', 11, array('unsigned' => true, 'default' => 60));
		$this->hasColumn('ccq_abandoned_resume_allowed', 'string', 5, array('default' => 'false'));
		$this->hasColumn('ccq_max_wait_time', 'integer', 11, array('unsigned' => true, 'default' => 0));
		$this->hasColumn('ccq_max_wait_time_no_agent', 'integer', 11, array('unsigned' => true, 'default' => 0));
		$this->hasColumn('ccq_max_wait_time_with_no_agent_time_reached ', 'integer', 11, array('unsigned' => true, 'default' => 5));
	}

	public function setUp()
	{
		$this->hasMany('callcenter_tier', array('local' => 'ccq_id', 'foreign'  => 'cct_ccq_id'));
		$this->hasOne('Location as queueLocation', array('local' => 'ccq_locationid', 'foreign' => 'location_id'));
		$this->hasMany('callcenter_queueNumber as Number', array('local' => 'ccq_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}

	public function getStrategies()
	{
		return array(
			'ring-all' => 'Ring all agents simultaneously',
			'longest-idle-agent' => 'Ring the agent who has been idle the longest',
			'agent-with-least-talk-time' => 'Ring the agent with least talk time',
			'agent-with-fewest-calls' => 'Ring the agent with fewest calls',
			'sequentially-by-agent-order' => 'Rings agents sequentially by tier & order'
		);
	}

	public function getMOHOptions()
	{
		return array(
			'local_stream://moh' => 'Music on Hold Local Stream',
			'local_stream://' => 'Other Local Stream',
			'${us-ring};loops=-1' => 'Default Ringing',
			'phrase:' => 'TTS',
			'${hold_music};loops=-1' => 'Default Music on Hold (Not Recommended)',
			'file://' => 'Specific File (Not Recommended)' ,
			'silence' => 'Silence'
		);
	}

	public function getTBSOptions()
	{
		return array(
			'system' => 'From the time the call entered the system',
			'queue' => 'From the time the call entered the queue'
		);
	}

	public static function getqueuesbylocation($locid)
	{
		$q = Doctrine_Query::create()->select('*')->from('callcenter_queue')->where('ccq_locationid = ?', $locid);
		return $q->fetchArray();
	}

	public function preValidate(Doctrine_Event $event)
	{
			$record = &$event->getInvoker();
			//echo 'record: <pre>' . print_r($record, true) . '</pre>';

			if ($record['ccq_id'] == 0 || is_null($record['ccq_id']))
			{
				$errorStack = $this->getErrorStack();
				$validator = Bluebox_Controller::$validation;

				$q = Doctrine_Query::create()->select('*')->from('callcenter_queue')->where('ccq_name = "' . $record['ccq_name'] . '" and ccq_locationid = ' . $record['ccq_locationid']);
				$agentcount = $q->execute()->count();

				if ($agentcount > 0)
				{
					$validator->add_error('callcenter_queue[ccq_name]', 'This queue already exists at this location.');
					$errorStack->add('ccq_name', 'queuedup');
					$validator->add_error('callcenter_queue[ccq_locationid]', 'queue agent already exists at this location.');
					$errorStack->add('ccq_locationid', 'queuedup');
				}
			}
	}

    public function contains($fieldName)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::contains('ccq_name');
    	else
    		return parent::contains($fieldName);
    }
	
	public function get($fieldName, $load = true)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::get('ccq_name', $load);
    	else
    		return parent::get($fieldName, $load);
    }
}

?>