<?php defined('SYSPATH') or die('No direct access allowed.');

class Sofia_Controller extends Bluebox_Controller {
    protected $baseModel = NULL;

    public function index() {

        $html = '';

        $this->template->content = new View('sofia/details');


        $interfaces = SofiaManager::getSIPInterfaces();


        foreach($interfaces as $sipinterface_id => $interface) {

            $html .= $this->showRegistrations(SofiaManager::getRegistrations($interface));
        }

        $this->view->modelContents = $html;

    }

    private function showRegistrations($registrations) {

       
 
        //$idx = array('call-id', 'user', 'contact', 'agent', 'status', 'host', 'network-ip', 'network-port', 'sip-auth-user', 'sip-auth-realm', 'mwi-account');
        $idx = array('user', 'contact', 'sip-auth-user', 'host', 'network-ip', 'network-port');
        $html = sprintf('<h1>sipinterface_%s</h1>', 'asdfasdfasdf'); //$registrations['interface']
        $html .= '<table width="100%" class="ui-widget">';

        $html .= '<tr class="ui-jqgrid-hdiv">';

        foreach($idx as $var) {
            $html .= '<th>' . ucwords(implode(' ', explode('-', $var))) . '</th>';
        }

        $html .= '</tr>';


        foreach($registrations as $registration) {
            
            $html .= '<tr>';
            foreach($idx as $var) {
                $html .= '<td>' . $registration[$var] . '</td>';
            }
            $html .= '</tr>';

        }
        $html.= "</table>";
        

        return $html;
    }
}
