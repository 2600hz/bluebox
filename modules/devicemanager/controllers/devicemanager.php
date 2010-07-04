<?php defined('SYSPATH') or die('No direct access allowed.');

class DeviceManager_Controller extends Bluebox_Controller
{  
    protected $baseModel = 'Device';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Devices'
            )
        );

        // Add the base model columns to the grid
        $grid->add('device_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('type', 'Type', array(
                'width' => '50',
                'search' => false,
            )
        );

        // Add the actions to the grid
        $grid->addAction('devicemanager/edit', 'Edit', array(
                'arguments' => 'device_id',
                'width' => '120'
            )
        );
        $grid->addAction('devicemanager/delete', 'Delete', array(
                'arguments' => 'device_id',
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
        if (!empty($data['device_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => str_replace('Device', ' Phone', $data['type']),
                'object_number_type' => 'DeviceNumber',
                'object_id' =>  $data['device_id'],
                'short_name' => 'device'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }
}
