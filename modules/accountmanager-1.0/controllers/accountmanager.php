<?php defined('SYSPATH') or die('No direct access allowed.');

class AccountManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Account';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Accounts'
            )
        );

        if (users::$user['user_type'] != User::TYPE_SYSTEM_ADMIN)
        {
            $grid->where('account_id = ', users::$user['account_id']);
        }

        // Add the base model columns to the grid
        $grid->add('account_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('type', 'Type', array(
                'callback' => array(
                    'function' => array('Account', 'typeName')
                )
            )
        );
        $grid->add('created_at', 'Created');

        // Add the actions to the grid
        $grid->addAction('accountmanager/edit', 'Edit', array(
                'arguments' => 'account_id',
            )
        );
        $grid->addAction('accountmanager/delete', 'Delete', array(
                'arguments' => 'account_id',
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function create()
    {
        if (users::$user['user_type'] != User::TYPE_SYSTEM_ADMIN)
        {
            message::set('You are not authorized to add an account!');

            $this->returnQtipAjaxForm(NULL);

            url::redirect(Router::$controller);
        }
        else
        {
            $this->session->delete('multitenant_account_id');
        }
        
        parent::create();
    }


    public function edit($id = NULL)
    {
        if (users::$user['user_type'] != User::TYPE_SYSTEM_ADMIN)
        {
            if (users::$user['account_id'] != $id)
            {
                message::set('You are not authorized to manage that account!');

                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router::$controller);
            }
        }
        else
        {
            $this->session->set('multitenant_account_id', $id);
        }
        
        parent::edit($id);
    }

    public function delete($id = NULL)
    {
        if (users::$user['user_type'] != User::TYPE_SYSTEM_ADMIN)
        {
            if (users::$user['account_id'] != $id)
            {
                message::set('You are not authorized to delete that account!');

                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router::$controller);
            }
        }
        else
        {
            $this->session->set('multitenant_account_id', $id);
        }

        parent::delete($id);
    }

    protected function save_prepare(&$object)
    {
        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
        
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        // TODO: This should be done by the plugins but there is no way to ensure
        // execution order and location must come first....
        $location = $this->input->post('location', array());

        $object['Location']->fromArray(array($location));

        $user = $this->input->post('user', array());

        $object['Location'][0]['User']->fromArray(array($user));

        $object['Location'][0]['User'][0]['user_type'] = User::TYPE_ACCOUNT_ADMIN;

        // TODO: This could be done by the plugin but since the others are here
        // we will put this here too...
        $contexts = array();

        if (!empty($_POST['context']['private']))
        {
            $contexts[] = array(
                'name' => empty($_POST['context']['private_name']) ? 'In-house Only' : $_POST['context']['private_name'],
                'locked' => FALSE
            );
        }

        if (!empty($_POST['context']['public']))
        {
            $contexts[] = array(
                'name' => empty($_POST['context']['public_name']) ? 'Publicly Accessible' : $_POST['context']['public_name'],
                'locked' => FALSE
            );
        }

        if (!empty($contexts))
        {
            $object['Context']->fromArray($contexts);
        }
        
        parent::save_prepare($object);
    }

    protected function post_save(&$object)
    {
        $object['Location'][0]['User'][0]['account_id'] = $object['account_id'];

        $object['Location'][0]['User'][0]->save();

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

        Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

        parent::post_save($object);
    }
}