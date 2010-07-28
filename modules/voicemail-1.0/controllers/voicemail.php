<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemail_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Voicemail';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Voicemail Boxes'
            )
        );

        // Add the base model columns to the grid
        $grid->add('voicemail_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Mailbox Name');
        $grid->add('mailbox', 'Mailbox');
        $grid->add('email_address', 'Email', array(
                'callback' => array(
                    'function' => array($this, '_showEmail'),
                    'arguments' =>  array('registry')
                )
            )
        );

        // Add the actions to the grid
        $grid->addAction('voicemail/edit', 'Edit', array(
                'arguments' => 'voicemail_id',
                'width' => '120'
            )
        );
        $grid->addAction('voicemail/delete', 'Delete', array(
                'arguments' => 'voicemail_id',
                'width' => '20'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function qtipAjaxReturn($data)
    {
        if (!empty($data['voicemail_id']))
        {
            $object['numbermanager'] = array(
                'object_name' => $data['name'],
                'object_description' => 'Voicemail Box',
                'object_number_type' => 'VoicemailNumber',
                'object_id' =>  $data['voicemail_id'],
                'short_name' => 'voicemail'
            );

            Event::run('ajax.updateobject', $object);
        }

        parent::qtipAjaxReturn($data);
    }
    
    public function _showEmail ($null, $registry)
    {
        if (!empty($registry['email_address']))
        {
            return $registry['email_address'];
        }
        
        return '';
    }
}

