<?php
class Odbc extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        //<param name="odbc-dsn" value="your_dsn_name:your_db_user:your_db_password"/>
		$this->hasColumn('odbc_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('dsn_name', 'string', 100, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('database', 'string', 100);
		$this->hasColumn('user', 'string', 100);
		$this->hasColumn('pass', 'string', 100); //encrypt?
		$this->hasColumn('host', 'string', 100);
		$this->hasColumn('port', 'integer', 5, array('unsigned' => TRUE));
		$this->hasColumn('type', 'string', 100);
		$this->hasColumn('description', 'string', 1500);
		//$this->hasColumn('enabled', 'boolean', NULL, array('default' => false));
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('OdbcMap', array('local' => 'odbc_id', 'foreign' => 'odbc_id', 'onDelete' => 'CASCADE'));

        // BEHAVIORS
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');
      }

}
