<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_agent extends Bluebox_Record
{
	public static $errors = array(
		'cca_loginid' => array(
			'notnull' => 'You must enter a login ID.',
			'notblank' => 'You must enter a login ID.',
			'agentdup' => 'This Agent already exists in this location.'
		),
			'cca_locationid' => array(
			'notnull' => 'You must select a location.',
			'notblank' => 'You must select a location.',
			'agentdup' => 'This Agent already exists in this location.'
		),
			'cca_type' => array(
			'notnull' => 'You must select an agent type.',
			'notblank' => 'You must select an agent type.'
		)
	);

	function setTableDefinition()
	{
		$this->hasColumn('cca_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('cca_loginid', 'string', 200, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('cca_locationid', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true));
		$this->hasColumn('cca_displayname', 'string', 200);
		$this->hasColumn('cca_type', 'string', 50, array('notnull' => true, 'notblank' => true, 'default' => 'callback'));
		$this->hasColumn('cca_contact', 'string', 1000, array('default' => '[call_timeout=10]user/{ext}@{domain}'));
		$this->hasColumn('cca_status', 'string', 50, array('default' => 'Logged Out'));
		$this->hasColumn('cca_max_no_answer', 'integer', 11, array('unsigned' => true, 'default' => 3));
		$this->hasColumn('cca_wrap_up_time', 'integer', 11, array('unsigned' => true, 'default' => 10));
		$this->hasColumn('cca_reject_delay_time', 'integer', 11, array('unsigned' => true, 'default' => 10));
		$this->hasColumn('cca_busy_delay_time', 'integer', 11, array('unsigned' => true, 'default' => 60));
	}

	public function setUp()
	{
		$this->hasMany('callcenter_tier', array('local' => 'cca_id',
			'foreign'  => 'cct_cca_id'));
		$this->hasOne('Location as agentLocation', array('local' => 'cca_locationid', 'foreign' => 'location_id'));

		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}

	public function getTypes()
	{
		return array(
			'callback' => 'Call Back',
			'uuid-standby' => 'UUID-Standby'
		);
	}

	public function getStatuses()
	{
		return array(
			'Logged Out' => 'Logged Out',
			'Available ' => 'Available',
			'Available (On Demand) ' => 'Available (On Demand)',
			'On Break' => 'On Break',
		);
	}

	public static function getagentsbylocation($locid)
	{
		$q = Doctrine_Query::create()->select('cca.*')->from('callcenter_agent cca')->where('cca_locationid = ' . $locid);
		return $q->fetchArray();
	}

	public function preValidate(Doctrine_Event $event)
	{
			$record = &$event->getInvoker();
			//echo 'record: <pre>' . print_r($record, true) . '</pre>';

			if ($record['cca_id'] == 0 || is_null($record['cca_id']))
			{
				$errorStack = $this->getErrorStack();
				$validator = Bluebox_Controller::$validation;

				$q = Doctrine_Query::create()->select('*')->from('callcenter_agent')->where('cca_loginid = "' . $record['cca_loginid'] . '" and cca_locationid = ' . $record['cca_locationid']);
				$agentcount = $q->execute()->count();

				if ($agentcount > 0)
				{
					$validator->add_error('callcenter_agent[cca_loginid]', 'This agent already exists at this location.');
					$errorStack->add('cca_loginid', 'agentdup');
					$validator->add_error('callcenter_agent[cca_locationid]', 'This agent already exists at this location.');
					$errorStack->add('cca_locationid', 'agentdup');
				}
			}
	}
}

?>