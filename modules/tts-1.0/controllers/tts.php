<?php defined('SYSPATH') or die('No direct access allowed.');

class Tts_Controller extends Bluebox_Controller
{
    protected $baseModel = 'TTSEngine';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Text to Speech Engines'
            )
        );

        // Add the base model columns to the grid
        $grid->add('tts_engine_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('description', 'Description');
        $grid->add('updated_at', 'Updated');

        // Add the actions to the grid
        $grid->addAction('tts/edit', 'Edit', array(
                'arguments' => 'tts_engine_id'
            )
        );
        $grid->addAction('tts/delete', 'Delete', array(
                'arguments' => 'tts_engine_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
}