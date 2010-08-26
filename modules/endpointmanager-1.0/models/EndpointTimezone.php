<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointTimezone extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_timezone_id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('tz', 'string', 10, array('notnull' => TRUE));
        $this->hasColumn('gmtoff', 'string', 10, array('notnull' => TRUE));
        $this->hasColumn('gmthr', 'string', 10, array('notnull' => TRUE));
    }

    public function setUp()
    {

    }
}