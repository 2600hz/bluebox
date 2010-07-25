<?php

class VoicemailViewer_Controller extends Bluebox_Controller {



    public function index()
    {

		$domain = 'voicemail_1';
		$user = '1000';
				
		$this->template->content = new View('voicemailviewer/index');
		$this->template->content->count = VoicemailManager::getCount($user, $domain);
		
		$list = VoicemailManager::getList($user, $domain);
		
		$this->template->content->list = $this->showMessages($list);
    }


    public function delete()
    {

    }
    
    public function play()
    {
		
	}
	
	private function showMessages($list)
	{

		$html = '<table width="100%">';
		//$idx = array('created_epoch', 'read_epoch', 'username', 'domain', 'path', 'uuid', 'cid-name', 'cid-number');
		$html .= '<tr><th>Received</th><th>Name</th><th>Number</th></tr>';
		foreach($list as $message)
		{
			$html .= '<tr>';
			$html .= '<td>' . date('h:i:s a m/d/Y', (int)$message['created_epoch']) .'</td>';
			$html .= '<td>' . $message['cid-name'] .'</td>';
			$html .= '<td>' . $message['cid-number'] .'</td>';
			$html .= '</tr>';
		}
		
		$html .= '<table>';
		
		return $html;
	}



}
