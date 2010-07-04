<?php defined('SYSPATH') or die('No direct access allowed.');

class Location extends Bluebox_Record
{
    public static $errors = array (
        'name' => array (
            'required' => 'Location name is required',
        ),
        'domain' => array (
            'required' => 'Domain is required',
            'unique' => 'This domain already exists'
        )
    );
    
    /**
     * Sets the table name, and defines table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('domain', 'string', 100, array('unique' => true, 'notnull' => true, 'notblank' => true));
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id'));
        $this->hasMany('Number', array('local' => 'location_id', 'foreign' => 'location_id', 'cascade' => array('delete')));
        $this->hasMany('User', array('local' => 'location_id', 'foreign' => 'location_id', 'cascade' => array('delete')));

        // BEHAVIORS
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}

