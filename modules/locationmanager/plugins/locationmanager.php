<?php defined('SYSPATH') or die('No direct access allowed.');

class LocationManager_Plugin extends Bluebox_Plugin
{
    protected $name = 'location';
    
    public function index()
    {
        $subview = new View('generic/grid');

        $subview->tab = 'main';
        
        $subview->section = 'general';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // Setup the base grid object
        $grid = jgrid::grid('Location', array(
                'caption' => 'Locations'
            )
        );

        // Add the base model columns to the grid
        $grid->add('location_id', 'ID', array(
                'hidden' => TRUE,
                'key' => TRUE
            )
        );
        $grid->add('name', 'Name');
        $grid->add('domain', 'Domain');


        // Add the actions to the grid
        $grid->addAction('locationmanager/edit', 'Edit', array(
                'arguments' => 'location_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('locationmanager/delete', 'Delete', array(
                'arguments' => 'location_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        
        // Produces the grid markup or JSON
        $subview->grid = $grid->produce();
        $subview->gridMenu = html::anchor('/locationmanager/create' ,'<span>Add New Location</span>', array('class' => 'qtipAjaxForm'));
        
        // Add our view to the main application
        $this->views[] = $subview;
    }
}