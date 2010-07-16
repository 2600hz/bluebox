<?php defined('SYSPATH') or die('No direct access allowed.');

class Call extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('call_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('core_field_1', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('core_field_2', 'array', 10000, array('default' => array()));
    }

    function setUp()
    {
        $this->actAs('GenericStructure');

        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
