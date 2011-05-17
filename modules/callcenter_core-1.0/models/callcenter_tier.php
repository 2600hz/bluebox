<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_tier extends Bluebox_Record
{
    public static $errors = array(
        'cct_cca_id' => array(
	    'notnull' => 'You must select an agent.',
            'tierdup' => 'This Agent is already a member of this queue.'
        ),
        'cct_ccq_id' => array(
	    'notnull' => 'You must select an queue.',
            'tierdup' => 'This Agent is already a member of this queue.'
        )
    );

    function setTableDefinition()
    {
        $this->hasColumn('cct_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true));
        $this->hasColumn('cct_cca_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('cct_ccq_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('cct_level', 'integer', 11, array('unsigned' => true, 'default' => 1));
        $this->hasColumn('cct_position', 'integer', 11, array('unsigned' => true, 'default' => 1));
    }

    public function setUp()
    {
        $this->hasOne('callcenter_agent', array('local' => 'cct_cca_id', 'foreign'  => 'cca_id'));
        $this->hasOne('callcenter_queue', array('local' => 'cct_ccq_id', 'foreign'  => 'ccq_id'));

		$this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }

    public function preValidate(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();
        $errorStack = $this->getErrorStack();
        $validator = Bluebox_Controller::$validation;

		if ($record['cct_id'] == 0)
		{
			$q = Doctrine_Query::create()->select('*')->from('callcenter_tier cct')->where('cct_cca_id = ' . $record['cct_cca_id'] . ' and cct_ccq_id = ' . $record['cct_ccq_id']);
			$tiercount = $q->execute()->count();
			if ($tiercount > 0)
			{
				$validator->add_error('callcenter_tier[cct_cca_id]', 'This agent is already a member of this queue.');
				$errorStack->add('cct_cca_id', 'tierdup');
				$validator->add_error('callcenter_tier[cct_ccq_id]', 'This agent is already a member of this queue.');
				$errorStack->add('cct_ccq_id', 'tierdup');
			}
		}
    }
}

?>