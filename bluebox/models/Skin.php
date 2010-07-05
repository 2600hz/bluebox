<?php defined('SYSPATH') or die('No direct access allowed.');

class Skin extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('skin_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 200, array('notnull' => true, 'notblank' => true, 'minlength' => 3));
        $this->hasColumn('location', 'string', 200, array('notnull' => true, 'notblank' => true, 'minlength' => 3));
        $this->hasColumn('default', 'boolean', array('notnull' => true, 'default' => false));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('Site', array('local' => 'skin_id', 'foreign' => 'skin_id'));

        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }
}

