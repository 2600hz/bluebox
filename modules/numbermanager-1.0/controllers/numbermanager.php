<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Number';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Numbers'
            )
        );

        // Add the base model columns to the grid
        $grid->add('number_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('number', 'Number', array(
                'callback' => array($this, '_formatNumber')
            )
        );
        $grid->add('number_route', 'Routes to', array(
                'callback' => array(
                    'arguments' => 'number_id',
                    'function' => array($this, '_showRoute')
                )
            )
        );
        $grid->add('pools', 'Number Pools', array(
                'align' => 'center',
                'callback' => array(
                    'arguments' => 'number_id',
                    'function' => array($this, '_showPools')
                )
            )
        );
        $grid->add('context', 'Contexts', array(
                'align' => 'center',
                'callback' => array(
                    'arguments' => 'number_id',
                    'function' => array($this, '_showContexts')
                )
            )
        );
        $grid->add('Location/name', 'Location', array(
                'width' => '150',
                'search' => false,
            )
        );

        // Add the actions to the grid
        $grid->addAction('numbermanager/edit', 'Edit', array(
                'arguments' => 'number_id'
            )
        );
        $grid->addAction('numbermanager/rebuild', 'Rebuild', array(
                'arguments' => 'number_id'
            )
        );
        $grid->addAction('numbermanager/delete', 'Delete', array(
                'arguments' => 'number_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function rebuild($number_id)
    {
        $this->loadBaseModel($number_id);

        $this->number->markModified('number');

        try
        {
            $this->number->save();

            message::set('Number ' .$this->number['number'] .' dialplan rebuild complete!', 'success');
        }
        catch (Exception $e)
        {
            message::set($e->getMessage());
        }
        
        $this->returnQtipAjaxForm();

        url::redirect(Router_Core::$controller);
    }

    public function create($class_type = NULL)
    {
        if (!empty($_REQUEST['create_class_type']))
        {
            $this->create_class_type = $_REQUEST['create_class_type'];
        }
        else if (!is_null($class_type))
        {
            $this->create_class_type = $class_type;
        }

        parent::create();
    }

    public function prepareUpdateView()
    {
        if (!empty($this->create_class_type))
        {
            $number_type = Doctrine::getTable('NumberType')->findOneByClass($this->create_class_type);

            if (!empty($number_type['number_type_id']))
            {
                $this->view->create_class_type = $this->create_class_type;

                $this->view->create_number_type = $number_type['number_type_id'];
            }
        }
        
        return parent::prepareUpdateView();
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data) AND $data instanceof Number)
        {
            $number = $data->toArray();

            if (!empty($_REQUEST['create_class_type']))
            {
                $number['class_type'] = $_REQUEST['create_class_type'];
            }

            $template = new View('numbermanager/avaliableNumber.mus', $number);
            
            $newObject = str_replace(array("\n", '  '), '', (string)$template);

            javascript::codeBlock('$(\'#number_inventory .avaliable_numbers\').append(\'' .$newObject .'\');');
            
            javascript::codeBlock('$(\'#avaliable_number_' .$data['number_id'].'\').trigger(\'click\');');
        }

        parent::qtipAjaxReturn($data);
    }

    public function _formatNumber($number)
    {
        return numbermanager::formatNumber($number);
    }
    
    public function _showRoute($NULL, $number_id)
    {
        return numbermanager::showRoute($number_id);
    }

    public function _showContexts($NULL, $number_id)
    {
        return numbermanager::showContexts($number_id);
    }

    public function _showPools($NULL, $number_id)
    {
        return numbermanager::showPools($number_id);
    }
}
