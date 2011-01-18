<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Jon Blanton <jon@2600hz.com>
 * @author Rockwood Cataldo <rocco@2600hz.com>
 * @license MPL
 * @package Xmpp
 */
class Xmpp_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Xmpp';

    // Need to load the dependClass.js
    public function __construct() {
        parent::__construct();
        javascript::add(array('jquery.dependClass.js'), 50);
    }

    public function index() {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'XMPP Clients'
            )
        );

        // Add the base model columns to the grid
        $grid->add('xmpp_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('login', 'XMPP Login');

        // Add the actions to the grid
        $grid->addAction('xmpp/edit', 'Edit', array(
                'arguments' => 'xmpp_id'
            )
        );
        $grid->addAction('xmpp/delete', 'Delete', array(
                'arguments' => 'xmpp_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;

        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function prepareUpdateView() {
        parent::prepareUpdateView();

        $this->view->outboundPatterns = Doctrine::getTable('SimpleRoute')->findAll(Doctrine::HYDRATE_ARRAY);
        $this->view->contexts = Doctrine::getTable('Context')->findAll(Doctrine::HYDRATE_ARRAY);
    }

    public function  save_succeeded(&$object)
    {
        Event::run('freeswitch.reload.dingaling');
        parent::save_succeeded($object);
    }

    public function  delete_succeeded(&$object)
    {
        Event::run('freeswitch.reload.dingaling');
        parent::delete_succeeded($object);
    }
}
