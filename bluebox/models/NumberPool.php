<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberPool extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('number_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true));
        $this->hasColumn('number_type_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true));
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Number', array('local' => 'number_id', 'foreign' => 'number_id'));
        $this->hasOne('NumberType', array('local' => 'number_type_id', 'foreign' => 'number_type_id'));
        
        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}
