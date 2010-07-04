<?php defined('SYSPATH') or die('No direct access allowed.');

class RingGroup_Controller extends Bluebox_Controller
{
    protected $baseModel = 'RingGroup';

    public function __construct()
    {
        parent::__construct();
        
        javascript::add('mustache');
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => ' Ring Groups'
            )
        );

        // Add the base model columns to the grid
        $grid->add('ring_group_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Ring Group Name');
        $grid->add('strategy', 'Strategy', array(
                'align' => 'center',
                'callback' => array($this, '_showStrategy')
            )
        );
        $grid->add('memberCount', 'Members', array(
                'search' => false,
                'align' => 'center',
                'callback' => array(
                    'function' => array($this, '_countMembers'),
                    'arguments' => array('ring_group_id')
                )
            )
        );

        // Add the actions to the grid
        $grid->addAction('ringgroup/edit', 'Edit', array(
                'arguments' => 'ring_group_id',
                'width' => '120'
            )
        );
        $grid->addAction('ringgroup/delete', 'Delete', array(
                'arguments' => 'ring_group_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function _countMembers($cell, $ring_group_id) {
        return 0;
        $rows = Doctrine::getTable('RingGroupMember')->findByRingGroupId($ring_group_id);

        $members = '';
        foreach($rows as $row)
        {
            $device = Doctrine::getTable('Device')->findOneByDeviceId($row['device_id']);
            if ($device)
            {
                $members .= '<p>' .$device->name .' (Device)</p>';
            }
        }

        if (empty($members))
        {
            return count($rows->toArray());
        } else {
            return "<a title='Ring Group Members' tooltip='" .$members ."' class='addInfo' href='#'>" .count($rows->toArray()) .'</a>';
        }
    }

    public function _showStrategy($cell)
    {
        switch($cell)
        {
            case 2:
                return 'Ring In Order';
            default:
                return 'Ring All';
        }
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data['ring_group_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => 'Ring Group',
                'object_number_type' => 'RingGroupNumber',
                'object_id' =>  $data['ring_group_id'],
                'short_name' => 'ringgroup'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }

    protected function prepareUpdateView()
    {
        $avaliableMemebers = array();

        Event::run('ringgroup.avaliablemembers', $avaliableMemebers);

        $this->view->members = json_encode(array_reverse($this->ringgroup['members']));

        $this->view->avaliableMembers = json_encode($avaliableMemebers);

        parent::prepareUpdateView();
    }

    protected function save_prepare(&$object)
    {
        $avaliableMemebers = array();

        Event::run('ringgroup.avaliablemembers', $avaliableMemebers);

        $members = array();

        foreach ($object['members'] as $key => $member)
        {
            if (empty($member['id']))
            {
                continue;
            }

            $idFilter = create_function('$array', 'return ( $array[\'id\'] == ' .$member['id'] .');');

            $memberDetails = array_filter($avaliableMemebers, $idFilter);

            $memberDetails = reset($memberDetails);

            if (empty($memberDetails['bridge']))
            {
                continue;
            }

            $members[] = array(
                'bridge' => $memberDetails['bridge'],
                'id' => $memberDetails['id'],
                'type' => $memberDetails['type'],
                'options' => array(
                    //'group_confirm_file' => '/path/to/prompt.wav',
                    //'group_confirm_key' => 4,
                    //'leg_timeout' => 60,
                    'ignore_early_media' => 'true'
                )
            );
        }

        $object['members'] = $members;

        parent::save_prepare($object);
    }
}
