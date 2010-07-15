<?php defined('SYSPATH') or die('No direct access allowed.');

class RingGroup extends Bluebox_Record
{
    const STRATEGY_PARALLEL = 0;
    const STRATEGY_SEQUENTIAL = 2;
    const STRATEGY_ENTERPRISE = 4;
    const STRATEGY_ROUNDROBIN = 6;
    
    function setTableDefinition()
    {
        $this->hasColumn('ring_group_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('strategy', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('members', 'array', 10000, array('default' => array()));
    }

    function setUp()
    {
        $this->hasMany('RingGroupNumber as Number', array('local' => 'ring_group_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id'));

        $this->actAs('GenericStructure');
        $this->actAs('MultiTenant');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }
}
