<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalXferSimple_Controller extends Bluebox_Controller
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
        $grid->addAction('externalxfersimple/edit', 'Edit', array(
                'arguments' => 'external_xfer_id'
            )
        );
        $grid->addAction('externalxfersimple/delete', 'Delete', array(
                'arguments' => 'external_xfer_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
    public function edit($id)
    {
	if (array_key_exists('selectednumber',$_POST)) {
		if (!array_key_exists('currentnumber',$_POST)) {
			$_POST['numbers']['assigned'][$_POST['selectednumber']]['number_id']=$_POST['selectednumber'];
		} elseif ($_POST['currentnumber']!=$_POST['selectednumber']) {
			$_POST['numbers']['assigned'][$_POST['selectednumber']]['number_id']=$_POST['selectednumber'];
			$_POST['numbers']['available'][$_POST['currentnumber']]['number_id']=$_POST['currentnumber'];
		}
	}
	parent::edit($id);
    }

    public function create()
    {
	if (array_key_exists('selectednumber',$_POST)) {
		$_POST['numbers']['assigned'][$_POST['selectednumber']]['number_id']=$_POST['selectednumber'];
	}
        parent::create();
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        $route_types = array();

        $trunkList = array();

        $interfaceList = array();

	$numbers = Doctrine_Query::Create()
		->select("Number.number_id,Number.number,Number.foreign_id")
		->from("Number,NumberPool,NumberType")
		->where("NumberType.class='ExternalXferNumber'")
		->andWhere("NumberType.number_type_id=NumberPool.number_type_id")
		->andWhere("NumberPool.number_id=Number.number_id");
	if ($this->externalxfer->external_xfer_id) {
		$numbers=$numbers->andWhere("(Number.class_type='' or Number.class_type IS NULL or Number.foreign_id=?)",$this->externalxfer->external_xfer_id);
	} else {
		$numbers=$numbers->andWhere("(Number.class_type='' or Number.class_type IS NULL )");
	}
	$numbers=$numbers->orderBy('Number.number')
		->execute(array(),Doctrine::HYDRATE_SCALAR);
	$this->view->numbers=array();
	$this->view->numberdefault=NULL;
	$matchcount=0;
	foreach ($numbers AS $number) {
		$this->view->numbers[$number['Number_number_id']]=$number['Number_number'];
		if ($this->externalxfer->external_xfer_id==$number['Number_foreign_id']) {
			$this->view->numberdefault=$number['Number_number_id'];
			$matchcount++;
		}
	}
	if ($matchcount>1) {
            message::set('This record cannot be edited, due to having multiple numbers');

            $this->returnQtipAjaxForm(NULL);

            url::redirect(Router_Core::$controller);
	}

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

        if (empty($numbers))
        {
            message::set('No extensions available!');

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
