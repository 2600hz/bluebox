<?php
class Group extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('group_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('account_id', 'integer', 11);
		$this->hasColumn('name', 'string', 100);
		$this->hasColumn('permission', 'array', 1024);
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	function setUp()
    {
        // RELATIONSHIPS
		//$this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id', 'onDelete' => 'CASCADE'));
		$this->hasMany('UserGroup', array('local' => 'group_id', 'foreign' => 'group_id'));
        // BEHAVIORS
		$this->actAs('Timestampable');
      }

}
