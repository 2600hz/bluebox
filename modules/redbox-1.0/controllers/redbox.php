<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Jon Blanton <jon@2600hz.com>
 * @license MPL
 * @package redbox-1.0
 */

class Redbox_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Redbox';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        $grid = jgrid::grid('Redbox', array(
                'caption' => 'Redbox Configuration'
            )
        );

        // Add the base model columns to the grid
        $grid->add('redbox_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );

        $grid->add('name', 'Redbox Name');
        $grid->add('port1_label', 'Port 1');
        $grid->add('port2_label', 'Port 2');
        $grid->add('port3_label', 'Port 3');

        $grid->addAction('redbox/edit', 'Edit', array(
                'arguments' => 'redbox_id'
            )
        );
        $grid->addAction('redbox/delete', 'Delete', array(
                'arguments' => 'redbox_id'
            )
        );

        $this->grid = $grid;

        plugins::views($this);

        $this->view->grid = $this->grid->produce();
    }

    public function  createView($baseModel = NULL, $forceDelete = NULL) {
        stylesheet::add('redbox');
        parent::createView($baseModel, $forceDelete);
    }

    public function  editView($baseModel = NULL, $forceDelete = NULL) {
        stylesheet::add('redbox');
        parent::createView($baseModel, $forceDelete);
    }
}