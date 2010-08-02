<?php

class Misdn extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('misdn_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('provider', 'string', 64);
        $this->hasColumn('port', 'string', 64);
    }
    
    
    public function setUp()
    {
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }
}