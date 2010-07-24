<?php defined('SYSPATH') or die('No direct access allowed.');

class Conference_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Conference';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Conferences'
            )
        );

        // Add the base model columns to the grid
        $grid->add('conference_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('registry', 'Record?', array(
                'callback' => array($this, '_showRecord')
            )
        );
        $grid->add('pins', 'Has Pin?', array(
                'callback' => array($this, '_hasPin')
            )
        );

        // Add the actions to the grid
        $grid->addAction('conference/edit', 'Edit', array(
                'arguments' => 'conference_id'
            )
        );
        $grid->addAction('conference/delete', 'Delete', array(
                'arguments' => 'conference_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function _showRecord($registry)
    {
        return (empty($registry['record']) ?'No' : 'Yes');
    }

    public function _showMOH($registry)
    {
        if (empty($registry['moh_type']))
        {
            return 'None';
        }
        
        switch ($registry['moh_type'])
        {
            case 1:
                return 'Account Default';

            default:
                return 'MOH';

        }   
    }

    public function _showPinType($pins)
    {
        return (ConferencePins::TYPE_MODERATOR ? 'Moderator' : 'Member');
    }

    public function _hasPin($pins)
    {
        if (!empty($pins) AND is_array($pins))
        {
            foreach($pins as $pin)
            {
                if (!empty($pin))
                {
                    return 'Yes';
                }
            }
        }

        return 'No';
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data['conference_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => 'Conference Bridge',
                'object_number_type' => 'ConferenceNumber',
                'object_id' =>  $data['conference_id'],
                'short_name' => 'conference'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }
}
