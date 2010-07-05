<?php defined('SYSPATH') or die('No direct access allowed.');

class User extends Bluebox_Record
{
    const TYPE_GUEST = 0;
    const TYPE_RESTRICTED_USER = 40;
    const TYPE_NORMAL_USER = 50;
    const TYPE_POWER_USER = 60;
    const TYPE_RESTRICTED_ADMIN = 70;
    const TYPE_ACCOUNT_ADMIN = 80;
    const TYPE_SYSTEM_ADMIN = 101;

    /**
     * @var string this holds an un-hashed copy of the password during a save cycle (for validation to run against)
     */
    protected $unHashedPassword = '';

    public static $errors = array (
        'first_name' => array (
            'required' => 'First name is required'
        ),
        'last_name' => array(
            'required' => 'Last name is required'
        ),
        'username' => array(
            'required' => 'Username is required',
            'duplicate' => 'Username is already in use'
        ),
        'email_address' => array (
            'required' => 'Email is required',
            'duplicate' => 'Email is already in use'
        ),
        'password' => array (
            'required' => 'Password is required',
            'length' => 'Must be 8-20 characters',
        )
    );

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('first_name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('last_name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('username', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('email_address', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('password', 'string', 64, array('notnull' => true, 'notblank' => true, 'minlength' => 8));
        $this->hasColumn('debug_level', 'integer', 11, array('unsigned' => true, 'default' => 0));
        $this->hasColumn('user_type', 'integer', NULL, array('notnull' => true, 'unsigned' => true, 'default' => 0));
        $this->hasColumn('logins', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('last_login', 'timestamp', NULL);
        $this->hasColumn('last_logged_ip', 'string', 40);
        $this->hasColumn('password_reset_token', 'string', '64');
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('Device', array('local' => 'user_id', 'foreign' => 'user_id'));
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id'));

        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
   }

    /**
     * Create an accessor for setting of passwords - hash them when they're set
     * NOTE: We use a Kohana function here, so this assumes Kohana is running! Could be dangerous...
     */
    public function setPassword($password)
    {
        if ($password) {
            // We need to tmp provide an un-hashed pwd string for validation
            $this->unHashedPassword = $password;
            return $this->_set('password', Auth::instance()->hash_password($password));
        }
    }
}
