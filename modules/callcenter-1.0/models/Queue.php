<?php defined('SYSPATH') or die('No direct access allowed.');

class Queue extends Bluebox_Record
{
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('queue_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 512, array('notblank' => true));
        //Store the rest in the registry
    }

    public function setUp()
    {
        $this->hasMany('Tier', array('local' => 'queue_id', 'foreign' => 'queue_id', 'cascade' => array('delete')));

        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}