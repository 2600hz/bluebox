<?php defined('SYSPATH') or die('No direct access allowed.');

class Tier extends Bluebox_Record
{
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('tier_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('queue_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('level', 'integer', 11, array('unsigned' => true, 'notnull'));
        //Store the rest in the registry
    }

    public function setUp()
    {
        $this->hasOne('Queue', array('local' => 'queue_id', 'foreign' => 'queue_id'));
        $this->hasMany('TierAgent', array('local' => 'tier_id', 'foreign' => 'tier_id', 'cascade' => array('delete')));

        $this->actAs('GenericStructure');
        $this->actAs('MultiTenant');
    }
}