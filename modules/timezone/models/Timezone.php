<?php

/**
 * Timezone.php doctrine record
 * @author freepbx team
 * @package FreePBX3
 * @subpackage Timezone
 */
class Timezone extends FreePbx_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('timezone_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('timezone', 'string', 100, array('notnull' => true, 'notblank' => true));

	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	function setUp()
    {
        // RELATIONSHIPS

        // BEHAVIORS
        $this->actAs('Polymorphic');

        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
	}

}

