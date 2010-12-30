<?php defined('SYSPATH') or die('No direct access allowed.');

class ContextManager_Plugin extends Bluebox_Plugin
{
    protected $name = 'context';

    public function index()
    {
        $subview = new View('generic/grid');

        $subview->tab = 'main';

        $subview->section = 'general';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // Setup the base grid object
        $grid = jgrid::grid('Context', array(
                'caption' => 'Contexts'
            )
        );

        // If there is a base model that contains an account_id,
        // then we want to show locations only that relate to this account
        $base = $this->getBaseModelObject();

        if ($base and !empty($base['account_id']))
        {
            // Set a where clause, if we're playing plug-in to someone else
            $grid->where('account_id = ', $base['account_id']);
        }

        // Add the base model columns to the grid
        $grid->add('context_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Context Name');

        // Add the actions to the grid
        $grid->addAction('contextmanager/edit', 'Edit', array(
                'arguments' => 'context_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('contextmanager/rebuild', 'Rebuild', array(
                'arguments' => 'context_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('contextmanager/delete', 'Delete', array(
                'arguments' => 'context_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );

        // Produces the grid markup or JSON
        $subview->grid = $grid->produce();
        $subview->gridMenu = html::anchor('/contextmanager/create' ,'<span>Add New Context</span>', array('class' => 'qtipAjaxForm'));
        
        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function initialAccountContext()
    {
        $subview = new View('contextmanager/initialAccountContext');

        $subview->tab = 'main';

        $subview->section = 'context';

        $subview->context = $this->input->post('context', array());

        // Add our view to the main application
        $this->views[] = $subview;
    }
}