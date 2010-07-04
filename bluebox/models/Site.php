<?php defined('SYSPATH') or die('No direct access allowed.');

class Site extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('site_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('url', 'string', 200, array('notnull' => true, 'notblank' => true, 'minlength' => 3));
        $this->hasColumn('homepage', 'string', 200, array('notnull' => true, 'default' => '/welcome'));
        $this->hasColumn('wildcard', 'boolean', array('notnull' => true, 'default' => true));
        $this->hasColumn('skin_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('default', 'boolean', array('notnull' => true, 'default' => false));
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Skin', array('local' => 'skin_id', 'foreign' => 'skin_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}

