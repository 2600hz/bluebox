<?php defined('SYSPATH') or die('No direct access allowed.');

class CallsFiles extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('calls_files_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('records_processed', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('records_errored', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('records_dup', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('filename', 'string', 100, array('notnull' => true, 'notblank' => true));
 
    }

    function setUp()
    {
        $this->actAs('GenericStructure');

        $this->actAs('Timestampable');
//        $this->actAs('MultiTenant');
    }
}
