<?php defined('SYSPATH') or die('No direct access allowed.');

class Endpoint extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
	$this->hasColumn('endpoint_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
	$this->hasColumn('name','string',512);
	$this->hasColumn('mac','string',12,array('notnull'=>true,'notblank'=>true));
	$this->hasColumn('brand','string',30,array('notnull'=>true,'notblank'=>true));
	$this->hasColumn('model','string',30,array('notnull'=>true,'notblank'=>true));
	$this->hasColumn('settings','string');
	$this->hasColumn('lines','string');
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    function setUp()
    {
        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
        $this->actAs('Timestampable');
    }
}

