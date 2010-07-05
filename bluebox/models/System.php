<?php defined('SYSPATH') or die('No direct access allowed.');

class System extends Bluebox_Record {
    function setTableDefinition()
    {
        $this->hasColumn('system_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100);
        $this->hasColumn('xml', 'blob');
    }

    function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }
}
