<?php defined('SYSPATH') or die('No direct access allowed.');

class Sofia_Controller extends Bluebox_Controller
{
    protected $baseModel = NULL;

    public function index()
    {

        $html = '';
        
        $this->template->content = new View('sofia/details');

		$eslManager = new EslManager();

		$sipInterface = Doctrine::getTable('SipInterface')->findAll();
		

		foreach($sipInterface as $interface)
		{
			$interfaceStr = 'sipinterface_' . $interface->sipinterface_id;

			$result = $eslManager->api('sofia xmlstatus profile ' . $interfaceStr);
			
			$xml = $eslManager->getResponse($result);

			$xml = simplexml_load_string($xml);

			$registrations = $xml->registrations->registration;			
			
			$html .= $this->showRegistrations($registrations, $interface->sipinterface_id);
		}

		$this->view->modelContents = $html;

    }
    
    public function details($type = NULL, $id = NULL)
    {
        switch ($type) {
            case 'registration':

   
             break; 
             
                
            default:
                message::set('Unable to find the details for ' .$type, array(
                    'type' => 'alert',
                    'redirect' => 'sofia'
                ));
        }        
    }

    private function showRegistrations($registrations, $id = NULL)
    {
        $this->cache = Cache::instance();
 
		$html = $this->cache->get('sofia_table_' .  $id);

		if (!$html) 
		{
			//$idx = array('call-id', 'user', 'contact', 'agent', 'status', 'host', 'network-ip', 'network-port', 'sip-auth-user', 'sip-auth-realm', 'mwi-account');
			$idx = array('user', 'contact', 'status', 'host', 'mwi-account');
			$html = sprintf('<h1>sipinterface_%d</h1>', $id);
			$html .= '<table width="100%">';
			
			$html .= '<tr>';
			
			foreach($idx as $var)
			{
				$html .= '<td>' . ucwords(implode(' ', explode('-', $var))) . '</td>';
			}
			
			$html .= '</tr>';	
			
			
			foreach($registrations as $registration)
			{
					$html .= '<tr>';
					foreach($idx as $var)
					{
						$html .= '<td>' . $registration->$var . '</td>';
					}
					$html .= '</tr>';
	   
			}
			$html.= "</table>";			
			$this->cache->set('sofia_table_' .  $id, $html, NULL, 10);
		}
        return $html;
    }
}
