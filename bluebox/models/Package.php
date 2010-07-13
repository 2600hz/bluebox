<?php defined('SYSPATH') or die('No direct access allowed.');

class Package extends Bluebox_Record
{
    /**
     * Sets the table name, and defines table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('package_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('display_name', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('version', 'string', 25, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'string', 25, array('notnull' => true));
        $this->hasColumn('status', 'string', 25, array('notnull' => true, 'default' => 'uninstalled'));
        $this->hasColumn('basedir', 'string', 255, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('navigation', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('Number', array('local' => 'package_id', 'foreign' => 'package_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}
