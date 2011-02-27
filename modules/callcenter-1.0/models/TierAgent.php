<?php defined('SYSPATH') or die('No direct access allowed.');

class TierAgent extends Bluebox_Record
{
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('tier_agent_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('tier_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('agent_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('position', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        //Store the rest in the registry
    }

    public function setUp()
    {
        $this->hasOne('Tier', array('local' => 'tier_id', 'foreign' => 'tier_id'));
        $this->hasOne('Agent', array('local' => 'agent_id', 'foreign' => 'agent_id'));

        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}