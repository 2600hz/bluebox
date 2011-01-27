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

        if (users::getAttr('user_type') != User::TYPE_SYSTEM_ADMIN)
        {
            $grid->where('account_id = ', users::getAttr('account_id'));
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
        if (users::getAttr('user_type') != User::TYPE_SYSTEM_ADMIN)
        {
            message::set('You are not authorized to add an account!');

            $this->returnQtipAjaxForm(NULL);

            url::redirect(Router::$controller);
        }
        else
        {
            users::restoreAccount();
        }
        
        parent::create();
    }

    public function edit($id = NULL)
    {
        if (users::getAttr('user_type') != User::TYPE_SYSTEM_ADMIN)
        {
            if (users::getAttr('account_id') != $id)
            {
                message::set('You are not authorized to manage that account!');

                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router::$controller);
            }
        }
        else
        {
            users::masqueradeAccount($id);
        }
        
        parent::edit($id);
    }

    public function delete($id = NULL)
    {
        if (users::getAttr('user_type') != User::TYPE_SYSTEM_ADMIN)
        {
            if (users::getAttr('account_id') != $id)
            {
                message::set('You are not authorized to delete that account!');

                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router::$controller);
            }
        }
        else
        {
            users::masqueradeAccount($id);
        }

        Session::instance()->set('bluebox.delete.unlimit', TRUE);

        parent::delete($id);

        Session::instance()->set('bluebox.delete.unlimit', FALSE);
    }

    protected function save_prepare(&$object)
    {
        if (!strcasecmp(Router::$method, 'create'))
        {
            Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

            Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

            Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

            // Skip this stuff if just an edit.
            if( ! $object->account_id)
            {
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
                        'name' => empty($_POST['context']['private_name']) ? 'Outbound Routes' : $_POST['context']['private_name'],
                        'locked' => FALSE,
                        'registry' => array('type' => 'private')
                    );
                }

                if (!empty($_POST['context']['public']))
                {
                    $contexts[] = array(
                        'name' => empty($_POST['context']['public_name']) ? 'Inbound Routes' : $_POST['context']['public_name'],
                        'locked' => FALSE,
                        'registry' => array('type' => 'public')
                    );
                }

                if (!empty($contexts))
                {
                    $object['Context']->fromArray($contexts);
                }
            }
        }
        
        parent::save_prepare($object);
    }

    protected function post_save(&$object)
    {
        if (!strcasecmp(Router::$method, 'create'))
        {
            $object['Location'][0]['User'][0]['account_id'] = $object['account_id'];

            $object['Location'][0]['User'][0]->save();

            Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

            Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

            Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

            if (!empty($object['account_id']))
            {
                $users_account_id = users::getAttr('account_id');

                users::masqueradeAccount($object['account_id']);

                // Initialize sample data
                Event::run('bluebox.account.initialize', $object);

                users::masqueradeAccount($users_account_id);
            }
        }

        parent::post_save($object);
    }
}