<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointmanTimeZones extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('idnum', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('tz', 'string', 10, array('notnull' => TRUE));
        $this->hasColumn('gmtoff', 'string', 10, array('notnull' => TRUE));
        $this->hasColumn('gmthr', 'string', 10, array('notnull' => TRUE));
    }

    public function setUp()
    {

    }
}