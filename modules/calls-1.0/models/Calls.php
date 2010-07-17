<?php defined('SYSPATH') or die('No direct access allowed.');

class Calls extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('calls_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('calls_uuid', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_accountcode', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_caller_id_number', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_destination_number', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_context', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_duration', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_start_stamp', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_answer_stamp', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_end_stamp', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_billsec', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_hangup_cause', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_channel_name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('calls_bridge_channel', 'string', 100, array('notnull' => true, 'notblank' => true));
    }

    function setUp()
    {
        $this->actAs('GenericStructure');

        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
