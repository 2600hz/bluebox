<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalXfer_Controller extends Bluebox_Controller
{
    protected $baseModel = 'ExternalXfer';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'External Destination'
            )
        );

        // Add the base model columns to the grid
        $grid->add('external_xfer_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('description', 'Description');

        // Add the actions to the grid
        $grid->addAction('externalxfer/edit', 'Edit', array(
                'arguments' => 'external_xfer_id'
            )
        );
        $grid->addAction('externalxfer/delete', 'Delete', array(
                'arguments' => 'external_xfer_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        $route_types = array();

        $trunkList = array();

        $interfaceList = array();

        if (class_exists('Trunk'))
        {
            $trunks = Doctrine::getTable('Trunk')->findAll(Doctrine::HYDRATE_ARRAY);

            foreach($trunks as $trunk)
            {
                $trunkList[$trunk['trunk_id']] = $trunk['name'];
            }

            if (!empty($trunkList))
            {
                $route_types[ExternalXfer::TYPE_TRUNK] = 'via Trunk';
            }
        }

        if (class_exists('SipInterface'))
        {
            $interfaces = Doctrine::getTable('SipInterface')->findAll(Doctrine::HYDRATE_ARRAY);

            foreach($interfaces as $interface)
            {
                $interfaceList[$interface['sipinterface_id']] = $interface['name'];
            }

            if (!empty($interfaceList))
            {
                $route_types[ExternalXfer::TYPE_SIP] = 'via SIP URI';
            }
        }

        if (empty($route_types))
        {
            message::set('No Trunk or Sip Interfaces avaliable to route external destinations through!');

            $this->returnQtipAjaxForm(NULL);

            url::redirect(Router_Core::$controller);
        }

        $this->view->trunks = $trunkList;

        $this->view->interfaces = $interfaceList;

        $this->view->route_types = $route_types;

        parent::prepareUpdateView($baseModel);
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data['external_xfer_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => 'External Transfer',
                'object_number_type' => 'ExternalXferNumber',
                'object_id' =>  $data['external_xfer_id'],
                'short_name' => 'externalxfer'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }
}