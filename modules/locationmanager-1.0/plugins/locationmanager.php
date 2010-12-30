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

        // If there is a base model that contains an account_id,
        // then we want to show locations only that relate to this account
        $base = $this->getBaseModelObject();

        if ($base and !empty($base['account_id']))
        {
            // Set a where clause, if we're playing plug-in to someone else
            $grid->where('account_id = ', $base['account_id']);
        }

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

    public function initialAccountLocation()
    {
        $subview = new View('locationmanager/initialAccountLocation');

        $subview->tab = 'main';

        $subview->section = 'location';

        $subview->location = $this->input->post('location', array());

        // Add our view to the main application
        $this->views[] = $subview;
    }
}