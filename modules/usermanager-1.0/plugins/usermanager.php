<?php defined('SYSPATH') or die('No direct access allowed.');

class UserManager_Plugin extends Bluebox_Plugin
{
    protected $preloadModels = 'User';

    public function initialAccountUsers()
    {
        $subview = new View('usermanager/initialAccountUsers');

        $subview->tab = 'main';

        $subview->section = 'users';

        $subview->user = $this->input->post('user', array());

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function appendToGrid()
    {
        $this->grid->add('User/full_name', 'Associated User', array(
            'width' => '100',
            'align' => 'left',
            'callback' => array(
                'function' => array(
                    $this,
                    'fullName'
                ) ,
                'arguments' => array(
                    'User/first_name',
                    'User/last_name'
                )
            ) ,
            'link' => array(
                'link' => 'usermanager/edit',
                'arguments' => 'user_id'
            ) ,
            'search' => false,
            'sortable' => false
        ));

        $this->grid->add('User/location', 'Location', array(
            'width' => '100',
            'align' => 'left',
            'callback' => array(
                'function' => array(
                    $this,
                    'location'
                ) ,
                'arguments' => array(
                    'user_id'
                )
            ) ,
            'search' => false,
            'sortable' => false
        ));
    }

    public function createSubGrid()
    {
        $subview = new View('generic/grid');

        $subview->tab = 'main';

        $subview->section = 'general';

        // Setup the base grid object
        $grid = jgrid::grid('User', array(
                'caption' => 'Users'
            )
        );
        
        // If there is a base model that contains an account_id,
        // then we want to show locations only that relate to this account
        $base = $this->getBaseModelObject();

        if ($base and !empty($base['location_id']))
        {
            // Set a where clause, if we're playing plug-in to someone else
            $grid->where('location_id = ', $base['location_id']);
        } 
        else if ($base and !empty($base['account_id']))
        {
            // Set a where clause, if we're playing plug-in to someone else
            $grid->where('account_id = ', $base['account_id']);
        }

        // Add the base model columns to the grid
        $grid->add('user_id', 'ID', array(
                'hidden' => TRUE,
                'key' => TRUE
            )
        );
        $grid->add('email_address', 'Email Address');
        $grid->add('first_name', 'First Name', array(
                'width' => '100',
                'search' => TRUE
            )
        );
        $grid->add('last_name', 'Last Name', array(
                'width' => '100',
                'search' => TRUE
            )
        );
        $grid->add('Location/name', 'Location', array(
                'hidden' => empty($base['location_id']) ? TRUE : FALSE,
                'width' => '100',
                'search' => TRUE,
                'sortable' => TRUE
            )
        );
        $grid->add('user_type', 'User Type', array(
                'callback' => array(
                    'function' => array($this, 'userType'),
                    'arguments' => array('user_type')
                )
            )
        );
        $grid->add('logins', 'Logins', array('hidden' => TRUE));
        $grid->add('last_login', 'Last Login', array('hidden' => TRUE));
        $grid->add('last_logged_ip', 'Last Logged IP', array('hidden' => TRUE));
        $grid->add('debug_level', 'Debug Level', array('hidden' => TRUE));

        // Add the actions to the grid
        $grid->addAction('usermanager/edit', 'Edit', array(
                'arguments' => 'user_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('usermanager/delete', 'Delete', array(
                'arguments' => 'user_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        if (users::$user->user_type == User::TYPE_SYSTEM_ADMIN) {
            $grid->addAction('usermanager/login', 'Login', array(
                'arguments' => 'user_id'
            ));
        }

        // Produces the grid markup or JSON
        $subview->grid = $grid->produce();

        $subview->gridMenu = html::anchor('/usermanager/create' ,'<span>Add New User</span>', array('class' => 'qtipAjaxForm'));

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function fullName($cell, $first_name, $last_name)
    {
        return $first_name .' ' .$last_name;
    }

    public function location($cell, $user_id)
    {
	$User = Doctrine::getTable('User')->find($user_id);

        return $User['Location']['name'];
    }

    public function userType($cell, $userType)
    {
        $userTypes = usermanager::getUserTypes();

        if (array_key_exists($userType, $userTypes))
        {
            return $userTypes[$userType];   
        }

        return 'Unknown';
    }
}

