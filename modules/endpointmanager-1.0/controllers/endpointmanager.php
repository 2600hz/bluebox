<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'EndpointmanMacList`';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Endpoints'
            )
        );

        // Add the base model columns to the grid
        $grid->add('id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('mac', 'MAC Address');
        $grid->add('model', 'Model');
        $grid->add('ext', 'Extension');
        $grid->add('description', 'Description');

        // Add the actions to the grid
        $grid->addAction('endpointmanager/edit', 'Edit', array(
                'arguments' => 'id'
            )
        );
        $grid->addAction('endpointmanager/delete', 'Delete', array(
                'arguments' => 'id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;

        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
}