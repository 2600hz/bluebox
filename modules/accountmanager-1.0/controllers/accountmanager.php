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
}
