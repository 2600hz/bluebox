<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_queues_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'callcenter_queue';

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('callcenter_queues', 50);
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Setup the base grid object
        $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Queues'
            )
        );
        // Add the base model columns to the grid
        $this->grid->add('ccq_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $this->grid->add('ccq_name', 'Name');
        //$grid->add('datafield2', 'Field 2');

        $this->grid->add('queueLocation/name', 'Location', array(
                'width' => '150',
                'search' => false,
            )
        );
        // Add the actions to the grid
        $this->grid->addAction('callcenter_queues/edit', 'Edit', array(
                'arguments' => 'ccq_id'
            )
        );
        $this->grid->addAction('callcenter_queues/delete', 'Delete', array(
                'arguments' => 'ccq_id'
            )
        );
        plugins::views($this);
        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
}