<?php defined('SYSPATH') or die('No direct access allowed.');

class ContextManager_Controller extends Bluebox_Controller
{
    public $baseModel = 'Context';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
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
            )
        );
        $grid->addAction('contextmanager/delete', 'Delete', array(
                'arguments' => 'context_id',
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
}