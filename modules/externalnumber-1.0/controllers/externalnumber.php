<?php defined('SYSPATH') or die('No direct access allowed.');

class ExternalNumber_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Number';

    public function  __construct()
    {
        parent::__construct();

        Doctrine::getTable('Number')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
        
        Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'External Numbers'
            )
        );

        // Add the base model columns to the grid
        $grid->add('number_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('number', 'Number', array(
                'width' => '120',
                'callback' => array($this, '_formatNumber')
            )
        );
        $grid->add('number_route', 'Routes to', array(
                'width' => '250',
                'search' => false,
                'sortable' => false,
                'callback' => array(
                    'arguments' => 'number_id',
                    'function' => array($this, '_showRoute')
                )
            )
        );
        $grid->add('account_id', 'Account', array(
                'width' => '150',
                'align' => 'center',
                'search' => false,
                'sortable' => false,
                'callback' => array(
                    'function' => array($this, '_showAccount')
                )
            )
        );
        $grid->add('context', 'Context', array(
                'width' => '150',
                'align' => 'center',
                'search' => false,
                'sortable' => false,
                'callback' => array(
                    'arguments' => 'number_id',
                    'function' => array($this, '_showContexts')
                )
            )
        );
        $grid->add('Location/name', 'Location', array(
                'width' => '150',
                'align' => 'center',
                'search' => false,
            )
        );
        $grid->where('type', '=', Number::TYPE_EXTERNAL);


        // Add the actions to the grid
        $grid->addAction('externalnumber/edit', 'Edit', array(
                'arguments' => 'number_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('externalnumber/delete', 'Delete', array(
                'arguments' => 'number_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }


    public function _formatNumber($number)
    {
        $matches = array();

        preg_match('/^\+?1?([2-9][0-8][0-9])([2-9][0-9][0-9])([0-9]{4})$/', $number, $matches);

        if (count($matches) == 4)
        {
            return '( '.$matches[1] .' ) ' .$matches[2] .' - ' .$matches[3];
        }
        else
        {
            return $number;
        }
    }
    
    public function _showRoute($NULL, $number_id)
    {
        $number = Doctrine::getTable('Number')->find($number_id);

        if (($number['class_type'] == '') and ($number['foreign_id'] == 0)) return '';

        try
        {
            $module = Doctrine::getTable(str_replace('Number', '', $number['class_type']))->find($number['foreign_id']);

            if (!$module)
            {
                return '';
            }

        }
        catch (Exception $e)
        {
            kohana::log('error', $e->getMessage());

            return '';
        }

        $base = substr($number['class_type'], 0, strlen($number['class_type']) - 6);

        if (isset($module['name']))
        {
             return $module['name'] . ' (' . $base . ')';
        }
        else
        {
            return '';
        }
    }

    public function _showContexts($NULL, $number_id)
    {
        $number = Doctrine::getTable('Number')->find($number_id);

        if (!empty($number['NumberContext'][0]['Context']['name']))
        {
            return $number['NumberContext'][0]['Context']['name'];
        }

        return '';
    }

    public function _showAccount($account_id)
    {
        $account = Doctrine::getTable('Account')->find($account_id);

        if ($name = arr::get($account, 'name'))
        {
            return $name;
        }

        return '';
    }

    protected function pre_save(&$object)
    {
        $object['type'] = Number::TYPE_EXTERNAL;

        $numberTypes = Doctrine::getTable('NumberType')->findAll();

        $assignedPools = array();
        
        foreach ($object['NumberPool'] as $numberPool)
        {
            $assignedPools[] = $numberPool['number_type_id'];
        }

        foreach ($numberTypes as $numberType)
        {
            if (!in_array( $numberType['number_type_id'], $assignedPools))
            {
                $object['NumberPool'][]['number_type_id'] = $numberType['number_type_id'];
            }
        }

        foreach ($object['NumberContext'] as $index => $context)
        {
            if ($index OR empty($context['context_id']))
            {
                unset($object['NumberContext'][$index]);
            }
        }

        if (empty($object['location_id']))
        {
            $object['location_id'] = 0;
        }

        try
        {
            if ($object['Destination']['account_id'] != $object['account_id'])
            {
                 $object['Destination'] = NULL;
                 
                 $object['class_type'] = NULL;

                 $object['foreign_id'] = 0;

            }
        }
        catch (Exception $e)
        {    
        }

        Doctrine::getTable(get_class($object))->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
    }
}