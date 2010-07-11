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
}