<?php defined('SYSPATH') or die('No direct access allowed.');

class Calls_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Calls';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Calls'
            )
        )
        // Add the base model columns to the grid
        ->add('call_id', 'ID', array(
                'hidden' => true,
                'key' => true
        ))
        ->add('name', 'Name')
        // Add the actions to the grid
        ->addAction('calls/edit', 'Edit', array(
                'arguments' => 'call_id',
                'width' => '120'
            )
        )
        ->addAction('calls/delete', 'Delete', array(
                'arguments' => 'call_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function download() {
        // Download a CDR

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Calls'
            )
        )
        // Add the base model columns to the grid
        ->add('call_id', 'ID', array(
                'hidden' => true,
                'key' => true
        ))
        ->add('name', 'Name')
        // Add the actions to the grid
        ->addAction('calls/edit', 'Edit', array(
                'arguments' => 'call_id',
                'width' => '120'
            )
        )
        ->addAction('calls/delete', 'Delete', array(
                'arguments' => 'call_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function downloadAsCsv($startDate, $endDate, $otherStuff) {
        // Actual download processing (after Csv is selected)
    }

    public function downloadAsXml($startDate, $endDate, $otherStuff) {
        // Actual download processing (after Xml is selected)
    }

    public function import() {
        // Manually import a CDR
        ProcessLog::importLogs();
    }
}
