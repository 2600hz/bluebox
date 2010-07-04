<?php defined('SYSPATH') or die('No direct access allowed.');

class Number extends Bluebox_Record
{
    /**
     * Constants for number status.
     */
    const STATUS_NORMAL = 0;
    const STATUS_SYSTEM = 1;
    const STATUS_LOCKED = 2;
    const STATUS_RESERVED = 4;

    /**
     * Constants for number type
     */
    const TYPE_INTERNAL = 0;
    const TYPE_EXTERNAL = 1;

    public static $description = __CLASS__;
    
    public static $errors = array(
        'number' => array (
            'unique' => 'This number already exists',
            'required' => 'Number is required',
            'default' => 'Number must be letters and numbers only'
        )
    );

    public static function statusName($status) {
        switch ($type) {
            case self::STATUS_NORMAL:
                return 'Normal';
            case self::STATUS_SYSTEM:
                return 'System';
            case self::STATUS_LOCKED:
                return 'Locked';
            case self::STATUS_RESERVED:
                return 'Reserved';
            default:
                return 'Unknown';
        }
    }

    public static function typeName($type) {
        switch ($type) {
            case self::TYPE_INTERNAL:
                return 'Internal';
            case self::TYPE_EXTERNAL:
                return 'External';
            default:
                return 'Unknown';
        }
    }

    /**
     * Sets the table name, and defines table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('number_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true, 'default' => 0));
        $this->hasColumn('number', 'string', 200, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('status', 'integer', 1, array('unsigned' => true, 'notnull' => true, 'default' => self::STATUS_NORMAL));
        $this->hasColumn('type', 'integer', 1, array('unsigned' => true, 'notnull' => true, 'default' => self::TYPE_INTERNAL));
        $this->hasColumn('dialplan', 'array', 10000, array('default' => array()));
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('NumberContext', array('local' => 'number_id', 'foreign' => 'number_id', 'cascade' => array('delete')));
        $this->hasMany('NumberPool', array('local' => 'number_id', 'foreign' => 'number_id', 'cascade' => array('delete')));
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id'));

        // BEHAVIORS
        $this->actAs('Polymorphic');
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
