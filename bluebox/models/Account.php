<?php defined('SYSPATH') or die('No direct access allowed.');

class Account extends Bluebox_Record
{
    /**
     * Constants for account types
     */
    const TYPE_NORMAL = 1;
    const TYPE_DEMO = 2;
    const TYPE_FREE = 3;
    const TYPE_BETA = 4;
    const TYPE_CLOSED = 5;

    /**
     * Constants for account status
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public static $display_column = 'name';

    /**
     * Error types to display mappings
     */
    public static $errors = array (
        'name' => array (
            'required' => 'Account name is required',
            'duplicate' => 'This account name already exists',
        )
    );

    public static $types = array(
        self::TYPE_NORMAL => 'Standard',
        self::TYPE_DEMO => 'Demo/Trial',
        self::TYPE_FREE => 'Free/Limited',
        self::TYPE_BETA => 'Beta Tester',
        self::TYPE_CLOSED => 'Closed/Canceled'
    );

    public static function typeName($type) {
        switch ($type) {
            case self::TYPE_DEMO:
                return 'Demo/Trial';
            case self::TYPE_NORMAL:
                return 'Normal';
            case self::TYPE_FREE:
                return 'Free/Limited';
            case self::TYPE_BETA:
                return 'Beta Tester';
            case self::TYPE_CLOSED:
                return 'Closed/Canceled';
            default:
                return 'Unknown';
        }
    }

    public static function statusName($status) {
        switch ($type) {
            case self::STATUS_ENABLED:
                return 'Enabled';
            case self::STATUS_DISABLED:
                return 'Disabled';
            default:
                return 'Unknown';
        }
    }

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('status', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 1));
        $this->hasColumn('type', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 0));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('Location', array('local' => 'account_id', 'foreign' => 'account_id', 'cascade' => array('delete')));

        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }
}

