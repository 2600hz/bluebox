<?php
class ExternalXfer extends FreePbx_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition() {
            // COLUMN DEFINITIONS
            $this->hasColumn('external_xfer_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
            $this->hasColumn('name', 'string', 80);
            $this->hasColumn('description', 'string', 512);
            $this->hasColumn('route_type', 'integer', 11);
            $this->hasColumn('route_details', 'array');
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp() {
        $this->hasOne('ExternalXferNumber as Number', array('local' => 'external_xfer_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }
}
