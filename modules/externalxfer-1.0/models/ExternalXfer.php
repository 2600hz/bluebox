<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalXfer extends Bluebox_Record
{
    const TYPE_TRUNK = 1;
    const TYPE_SIP = 2;

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
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
    public function setUp() 
    {
        $this->hasMany('ExternalXferNumber as Number', array('local' => 'external_xfer_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}