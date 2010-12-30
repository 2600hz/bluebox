<?php defined('SYSPATH') or die('No direct access allowed.');

class SimpleRoute extends Bluebox_Record
{
    const TYPE_SIMPLE = 1;
    const TYPE_REGEX = 2;
    
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('simple_route_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 80, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'integer', 11, array('default' => self::TYPE_SIMPLE));
        $this->hasColumn('patterns', 'array',  NULL, array('default' => array(NULL)));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        $this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id'));
        
        Doctrine::getTable('Account')->bind(array('SimpleRoute', array('local' => 'account_id', 'foreign' => 'account_id', 'cascade' => array('delete'))), Doctrine_Relation::MANY);

        $this->actAs('GenericStructure');
        $this->actAs('MultiTenant');
    }
}