<?php defined('SYSPATH') or die('No direct access allowed.');

class CallsTemplate extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('callstemplate_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
    }

    function setUp()
    {
        $this->actAs('GenericStructure');

        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
