<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDay extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('time_of_day_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 512, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('time', 'string', 100, array('notnull' => true, 'notblank' => true, 'default' => '540;1020'));
        $this->hasColumn('mon', 'string', 5, array('notnull' => true, 'notblank' => true, 'default' => '1'));
        $this->hasColumn('tue', 'string', 5, array('notnull' => true, 'notblank' => true, 'default' => '1'));
        $this->hasColumn('wen', 'string', 5, array('notnull' => true, 'notblank' => true, 'default' => '1'));
        $this->hasColumn('thur', 'string', 5, array('notnull' => true, 'notblank' => true, 'default' => '1'));
        $this->hasColumn('fri', 'string', 5, array('notnull' => true, 'notblank' => true, 'default' => '1'));
        $this->hasColumn('sat', 'string', 5, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('sun', 'string', 5, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('during_number_id', 'string', 32, array('default' => NULL));
        $this->hasColumn('outside_number_id', 'string', 32, array('default' => NULL));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('TimeOfDayNumber as Number', array('local' => 'time_of_day_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));
        
        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
        $this->actAs('Timestampable');
    }
}

