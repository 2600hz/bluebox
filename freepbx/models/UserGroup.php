<?php
class UserGroup extends FreePbx_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('user_group_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('user_id', 'integer', 11, array('unsigned' => true));
		$this->hasColumn('group_id', 'integer', 11, array('unsigned' => true));
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	function setUp()
    {
        // RELATIONSHIPS
		$this->hasMany('Group', array('local' => 'group_id', 'foreign' => 'group_id', 'onDelete' => 'CASCADE'));
        // BEHAVIORS

        $this->actAs('Timestampable');
      }

}
