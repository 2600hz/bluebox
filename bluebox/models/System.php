<?php defined('SYSPATH') or die('No direct access allowed.');

class System extends Bluebox_Record {
    function setTableDefinition()
    {
        $this->hasColumn('system_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100);
        $this->hasColumn('xml', 'blob');
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    function setUp()
    {
        $this->actAs('Timestampable');
    }
}
