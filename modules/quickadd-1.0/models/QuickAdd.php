<?php defined('SYSPATH') or die('No direct access allowed.');

class QuickAdd extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('quickadd_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 40, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('locked', 'boolean', NULL, array('default' => false));
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
