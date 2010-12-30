<?php defined('SYSPATH') or die('No direct access allowed.');

class UserManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'User';

    public function index()
    {
        $this->template->content = new View('generic/grid');
        
        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Users'
            )
        );

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
                'arguments' => 'user_id'
            )
        );
        $grid->addAction('usermanager/delete', 'Delete', array(
                'arguments' => 'user_id'
            )
        );
        if (users::getAttr('user_type') == User::TYPE_SYSTEM_ADMIN) {
            $grid->addAction('usermanager/login', 'Login', array(
                'arguments' => 'user_id'
            ));
        }
        
        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function login($userId)
    {
        if (users::getAttr('user_type') == User::TYPE_SYSTEM_ADMIN)
        {
            users::masqueradeUser($userId);

            url::redirect('/');
        } 
        else
        {
            Event::run('system.404');

            die();
        }
    }

    public function restore()
    {
        users::restoreUser();

        url::redirect('/');
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

    public function qtipAjaxReturn($data)
    {
        if (!empty($data->user_id))
        {

            $fullName = $data->first_name .' ' .$data->last_name;

            javascript::codeBlock('$(\'.users_dropdown\').append(\'<option value="' .$data->user_id .'" selected="selected">' .$fullName  .'</option>\');');
        }
        
        parent::qtipAjaxReturn($data);
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        $this->view->password = isset($_POST['user']['create_password']) ? $_POST['user']['create_password'] : '';

        $this->view->confirm_password = isset($_POST['user']['confirm_password']) ? $_POST['user']['confirm_password'] : '';

        parent::prepareUpdateView($baseModel);
    }
}
