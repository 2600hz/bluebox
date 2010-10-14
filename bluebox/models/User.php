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
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id', 'onDelete' => 'SET NULL'));

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
        if ($password)
        {
            // We need to tmp provide an un-hashed pwd string for validation
            $this->unHashedPassword = $password;
            
            return $this->_set('password', Auth::instance()->hash_password($password));
        }
    }

    public function preDelete(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        if ($record['user_type'] == User::TYPE_SYSTEM_ADMIN)
        {
            if ($this->countUserType(User::TYPE_SYSTEM_ADMIN) <= 1)
            {
                throw new Exception('You can not delete the only system admin!');
            }
        }
    }

    public function preUpdate(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        $modified = $this->getModified(TRUE);

        if (array_key_exists('user_type', $modified) AND $modified['user_type'] == User::TYPE_SYSTEM_ADMIN)
        {
            if ($this->countUserType(User::TYPE_SYSTEM_ADMIN) <= 1)
            {
                throw new Exception('You can not deallocate the only system admin!');
            }
        }
    }

    public function preValidate(Doctrine_Event $event)
    {
        $user = input::instance()->post('user', array());

        $record = &$event->getInvoker();

        $errorStack = $this->getErrorStack();

        $validation = Bluebox_Controller::$validation;

        if (!isset($user['username']) AND isset($user['email_address']))
        {
            $record['username'] = $user['email_address'];
        }

        if (!empty($user['create_password']))
        {
            $record['password'] = $user['create_password'];
        }

        if (Router::$method == 'create')
        {
            if (empty($user['create_password']))
            {
                $validation->add_error('user[create_password]', 'Please provide a password');

                $errorStack->add('password', 'notblank');
            }

            if (empty($user['confirm_password']))
            {
                $validation->add_error('user[confirm_password]', 'Please confirm your password');

                $errorStack->add('password', 'noconfirm');
            }

            if ($user['confirm_password'] !== $user['create_password'])
            {
                $validation->add_error('user[confirm_password]', 'Password does not match');

                $errorStack->add('password', 'nomatch');
            }

        }
        else if (!empty($user['create_password']))
        {
            if (empty($user['confirm_password']))
            {
                $validation->add_error('user[confirm_password]', 'Please confirm your password');

                $errorStack->add('password', 'noconfirm');
            }

            if ($user['confirm_password'] !== $user['create_password'])
            {
                $validation->add_error('user[confirm_password]', 'Password does not match');

                $errorStack->add('password', 'nomatch');
            }
        }

        $enforce = Kohana::config('core.pwd_complexity');

        if (empty($enforce) || empty($user['create_password']))
        {
            return TRUE;
        }

        // at least one digit
        if (!preg_match('/[0-9]{1,}/', $user['create_password']))
        {
            $validation->add_error('user[create_password]', 'Password must contain digits and letters');

            $errorStack->add('password', 'nocomplexity');
        }

        // at least one character
        if (!preg_match('/[A-Za-z]{1,}/', $user['create_password']))
        {
            $validation->add_error('user[create_password]', 'Password must contain digits and letters');

            $errorStack->add('password', 'nocomplexity');
        }
    }

    public function countUserType($user_type)
    {
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        $users = $this->getTable()->findByUserType(User::TYPE_SYSTEM_ADMIN);

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

        return count($users);
    }
}
