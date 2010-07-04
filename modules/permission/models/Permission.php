<?php
class Permission extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('permission_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('controller', 'string', 20, array('notnull' => TRUE, 'default' => ''));
        $this->hasColumn('method', 'string', 20, array('notnull' => TRUE, 'default' => ''));
        $this->hasColumn('permission', 'string', 20, array('notnull' => TRUE, 'default' => ''));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        $this->hasOne('User', array('local' => 'user_id', 'foreign' => 'user_id', 'onDelete' => 'CASCADE'));
        $this->actAs('Timestampable');
    }
}
