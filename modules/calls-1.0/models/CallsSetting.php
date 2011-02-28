<?php defined('SYSPATH') or die('No direct access allowed.');

class CallsSetting extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('callssetting_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
    }

    function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');
//        $this->actAs('MultiTenant');
    }
}
