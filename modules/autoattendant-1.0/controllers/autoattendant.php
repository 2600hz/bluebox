<?php defined('SYSPATH') or die('No direct access allowed.');

class AutoAttendant_Controller extends Bluebox_Controller
{
    protected $baseModel = 'AutoAttendant';

    public function __construct()
    {
        parent::__construct();

        stylesheet::add('autoattendant', 50);

        javascript::add('mustache');
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Auto Attendants'
            )
        );

        // Add the base model columns to the grid
        $grid->add('auto_attendant_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name', array(
                'width' => '200',
                'search' => false,
            )
        );
        $grid->add('type', 'Prompt', array(
                'align' => 'center',
                'callback' => array(
                    'function' => array($this, '_showPrompt'),
                    'arguments' => array('registry')
                )
            )
        );
        $grid->add('keys', 'Options', array(
                'align' => 'center',
                'callback' => array(
                    'function' => array($this, '_showOptions'),
                    'arguments' => array('keys')
                )
            )
        );

        // Add the actions to the grid
        $grid->addAction('autoattendant/edit', 'Edit', array(
                'arguments' => 'auto_attendant_id'
            )
        );
        $grid->addAction('autoattendant/delete', 'Delete', array(
                'arguments' => 'auto_attendant_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }


    protected function prepareUpdateView()
    {
        $numberingOptions = array(
            'number_pools' => array(array('value' => 0, 'text' => 'Select')),
            'destinations' => array(array('value' => 0, 'text' => 'Select'))
        );

        $numberTypes = Doctrine::getTable('NumberType')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach($numberTypes as $numberType)
        {
            $numbers = Doctrine_Query::create()
                ->select('n.number_id, n.number, d.name')
                ->from('Number n, n.' .str_replace('Number', '', $numberType['class']) .' d')
                ->whereNotIn('n.foreign_id', array(0, 'NULL'))
                ->andWhereIn('n.class_type', array($numberType['class']))
                ->orderBy('number')
                ->execute(array(), Doctrine::HYDRATE_SCALAR);

            if (empty($numbers))
            {
                continue;
            }

            $numberingOptions['number_pools'][] = array(
                'value' => $numberType['class'],
                'text' => $numberType['class']
            );
            
            foreach($numbers as $number)
            {
                $numberingOptions['destinations'][] = array(
                    'value' => $number['n_number_id'],
                    'text' => $number['d_name'] .' (' .$number['n_number'] .')',
                    'class' => $numberType['class'],
                );
            }
        }

        $this->view->numberingJson = json_encode($numberingOptions);
        
        $this->view->keys = json_encode($this->autoattendant['keys']);

        parent::prepareUpdateView();
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data['auto_attendant_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => 'Auto Attendant',
                'object_number_type' => 'AutoAttendantNumber',
                'object_id' =>  $data['auto_attendant_id'],
                'short_name' => 'autoattendant'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }

    public function _showOptions($keys)
    {
        if (empty($keys))
        {
            return 'None';
        }

        $options = '';

        foreach($keys as $key)
        {
            $dest = numbering::getDestinationByNumber($key['number_id']);

            if ($dest)
            {
                $options .= '<p>Option ' .$key['digits'] .' routes to ' .$dest->name .' ('.get_class($dest) .')</p>';
            } 
            else
            {
                $options .= '<p>The destination for option ' .$key['digits'] .' was removed</p>';
            }
        }
        
        if (empty($options))
        {
            return 'None';
        } 
        else
        {
            $options = str_replace('\'', '', $options);

            return "<a title='Auto Attendant Options' tooltip='" .$options ."' class='addInfo' href='#'>" .count($keys) .'</a>';
        }
    }

    public function _showPrompt($null, $registry)
    {
        if (empty($registry['type']))
        {
            return '';
        }

        switch($registry['type'])
        {
            case 'tts':
                return "<a title='Text to Speech' tooltip='" .$registry['tts_string'] ."' class='addInfo' href='#'>Text to Speech</a>";

            case 'audio':
                if (!class_exists('MediaFile') OR empty($registry['mediafile_id']))
                {
                     return '<span style="color:red">MISSING AUDIO FILE</span>';
                }

                $mediaFile = Doctrine::getTable('MediaFile')->find($registry['mediafile_id']);

                if (!$mediaFile)
                {
                    return '<span style="color:red">MISSING AUDIO FILE</span>';
                }

                return str_replace(array('en/us/callie/'), '', $mediaFile['file']);

            default:
                return 'unknown';
            
        }
    }
}
