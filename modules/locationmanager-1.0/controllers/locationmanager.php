<?php defined('SYSPATH') or die('No direct access allowed.');

class LocationManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Location';

    public function  __construct()
    {
        parent::__construct();
        
        if (users::$user->user_type <= User::TYPE_ACCOUNT_ADMIN)
        {
            message::set('You are not authorized to manage a location!');

            $this->returnQtipAjaxForm(NULL);

            url::redirect('/');
        }   
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Locations'
            )
        );

        // Add the base model columns to the grid
        $grid->add('location_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('domain', 'Domain Name/Realm');

        // Add the actions to the grid
        $grid->addAction('locationmanager/edit', 'Edit', array(
                'arguments' => 'location_id',
                'width' => '120'
            )
        );
        $grid->addAction('locationmanager/delete', 'Delete', array(
                'arguments' => 'location_id',
                'width' => '20'
            )
        );
        
        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
}
