<?php defined('SYSPATH') or die('No direct access allowed.');

class Agent extends Bluebox_Record
{
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('agent_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 16, array('notblank' => true));
        $this->hasColumn('type', 'string', 20, array('notblank' => true));
        $this->hasColumn('device_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        //Store the rest in the registry
    }

    public function setUp()
    {
        $this->hasOne('Device', array('local' => 'device_id', 'foreign' => 'device_id'));
        $this->hasMany('TierAgent', array('local' => 'agent_id', 'foreign' => 'agent_id', 'cascade' => array('delete')));

        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}