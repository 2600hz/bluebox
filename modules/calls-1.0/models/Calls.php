<?php defined('SYSPATH') or die('No direct access allowed.');

class Calls extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('calls_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('uuid', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('accountcode', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('caller_id_number', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('destination_number', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('context', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('duration', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('start_stamp', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('answer_stamp', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('end_stamp', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('billsec', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('hangup_cause', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('channel_name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('bridge_channel', 'string', 100, array('notnull' => true, 'notblank' => true));
    }

    function setUp()
    {
        $this->actAs('GenericStructure');

        $this->actAs('Timestampable');
//        $this->actAs('MultiTenant');
    }
}
