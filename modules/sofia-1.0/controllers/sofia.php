<?php defined('SYSPATH') or die('No direct access allowed.');

class Sofia_Controller extends Bluebox_Controller
{
    public $baseModel = 'SipRegistrations';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Sip Registrations'
            )
        );

        // Add the base model columns to the grid
        $grid->add('call_id', 'Call ID', array(
                'hidden' => true,
                'key' => true
            )
        );

        $grid->add('sip_user', 'User', array(
                'width' => '20',
                'search' => true
            )
        );
        $grid->add('sip_host', 'Host', array(
                'width' => '20',
                'search' => true
            )
        );
        $grid->add('contact', 'Contact', array(
                'width' => '50',
                'search' => true
            )
        );
        $grid->add('status', 'Status', array(
                'width' => '20',
                'search' => true
            )
        );
        $grid->add('user_agent', 'User Agent', array(
                'width' => '50',
                'search' => true
            )
        );

        // Add the actions to the grid
        $grid->addAction('sofia/details/registration', 'Details', array(
                'arguments' => 'call_id',
                'width' => '120'
            )
        );
//        $grid->addAction('sofia/reboot', 'Reboot Phone', array(
//                'arguments' => 'call_id',
//                'width' => '120'
//            )
//        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
    
    public function details($type = NULL, $id = NULL)
    {
        switch ($type) {
            case 'registration':
                $baseModel = Doctrine::getTable('SipRegistrations')->find($id);
                if ($baseModel) {
                    $this->view->modelContents = $this->displayModelContents($baseModel);
                    break;
                } 
                
            default:
                message::set('Unable to find the details for ' .$type, array(
                    'type' => 'alert',
                    'redirect' => 'sofia'
                ));
        }        
    }

    private function displayModelContents($model)
    {
        $html = "<table class=\"fancy\" width=\"100%\">";

        foreach($model as $key => $value) {
            if ($key == 'network_ip') {
                $html .= '<tr>';
                $html .=  '<td>' .$key .'</td>';
                $html .= '<td><a href="http://' .$value .'" target="_blank">' .htmlspecialchars($value) .'</a></td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td>' .$key .'</td>';
                $html .= '<td>' .htmlspecialchars($value) .'</td>';
                $html .= '</tr>';
            }
            
        }

        $html.= "</table>";
        
        return $html;
    }
}
