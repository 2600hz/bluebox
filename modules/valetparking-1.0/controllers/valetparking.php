<?php defined('SYSPATH') or die('No direct access allowed.');
class valetparking_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'ValetParkingLot';

    /**
     * Typically we create a grid, but you can define any entry point you
     * would like...
     */
    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Valet Parking Lots'
            )
        );

        // Add the base model columns to the grid
        $grid->add('vpl_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('vpl_name', 'Name');
        $grid->add('vpl_start', 'Start');
        $grid->add('vpl_end', 'End');
        
        // Add the actions to the grid
        $grid->addAction('valetparking/edit', 'Edit', array(
                'arguments' => 'vpl_id'
            )
        );
        $grid->addAction('valetparking/delete', 'Delete', array(
                'arguments' => 'vpl_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;

        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
}