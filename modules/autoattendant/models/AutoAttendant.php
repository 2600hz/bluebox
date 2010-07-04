<?php defined('SYSPATH') or die('No direct access allowed.');

class AutoAttendant extends Bluebox_Record
{
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('auto_attendant_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));

        $this->hasColumn('name', 'string', 512, array('notblank' => true));

        $this->hasColumn('description', 'string',  4096);

        $this->hasColumn('timeout' , 'integer', 11, array('notblank' => true, 'default' => 10));

        $this->hasColumn('digit_timeout' , 'integer', 11, array('notblank' => true, 'default' => 2));

        $this->hasColumn('extension_context_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));

        $this->hasColumn('extension_digits', 'integer', 11, array('unsigned' => true, 'default' => 4));

        $this->hasColumn('keys', 'array', 10000, array('default' => array()));

        $this->hasColumn('registry', 'array', 10000, array('default' => array()));

        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    public function setUp()
    {
        $this->hasMany('AutoAttendantNumber as Number', array('local' => 'auto_attendant_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));
        
        // BEHAVIORS
        $this->actAs('TelephonyEnabled');
        
        $this->actAs('MultiTenant');
    }
}
