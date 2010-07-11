<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDay_Controller extends Bluebox_Controller
{
    protected $baseModel = 'TimeOfDay';
    
    public function __construct()
    {
        parent::__construct();

        stylesheet::add('jslider', 50);
        
        javascript::add(array('jquery.dependClass.js', 'jquery.slider.js'), 50);
    }
    
    /**
     * Method for the main page of this module
     */
    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Time Of Day Routes'
            )
        );

        // Add the base model columns to the grid
        $grid->add('time_of_day_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Route Name');
        
        // Add the actions to the grid
        $grid->addAction('timeofday/edit', 'Edit', array(
                'arguments' => 'time_of_day_id',
                'width' => '120'
            )
        );
        $grid->addAction('timeofday/delete', 'Delete', array(
                'arguments' => 'time_of_day_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data['time_of_day_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => 'Time Of Day',
                'object_number_type' => 'TimeOfDayNumber',
                'object_id' =>  $data['time_of_day_id'],
                'short_name' => 'timeofday'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }
}