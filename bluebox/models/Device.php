<?php defined('SYSPATH') or die('No direct access allowed.');

class Device extends Bluebox_Record
{
    public static $errors = array (
        'name' => array (
            'required' => 'Device name is required'
        )
    );

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('device_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));

        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true));

        $this->hasColumn('name', 'string', 100, array('default' => '', 'notnull' => true, 'notblank' => true));

        $this->hasColumn('type', 'string', 50, array('default' => 'SipDevice'));

        $this->hasColumn('registry', 'array', 10000, array('default' => array()));

        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('User', array('local' => 'user_id', 'foreign' => 'user_id'));

        $this->hasMany('DeviceNumber as Number', array('local' => 'device_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        // BEHAVIORS
        $this->actAs('Timestampable');

        $this->actAs('TelephonyEnabled');
        
        $this->actAs('MultiTenant');
    }
}
