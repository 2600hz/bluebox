<?php defined('SYSPATH') or die('No direct access allowed.');
class sharedpresence_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'SharedPresenceDB';

    /**
     * Typically we create a grid, but you can define any entry point you
     * would like...
     */
    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Shared Presence Databases'
            )
        );

        // Add the base model columns to the grid
        $grid->add('spd_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('spd_name', 'Name');
        
        // Add the actions to the grid
        $grid->addAction('sharedpresence/edit', 'Edit', array(
                'arguments' => 'spd_id'
            )
        );
        $grid->addAction('sharedpresence/delete', 'Delete', array(
                'arguments' => 'spd_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;

        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
        
}