<?php defined('SYSPATH') or die('No direct access allowed.');

class XmlcdrSetting extends Bluebox_Record
{    
    function setTableDefinition()
    {
        $this->hasColumn('xmlcdrsetting_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('fshost', 'string', 100, array('notnull' => true, 'notblank' => true));
    }

    function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');
    }
}
