<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * Module:
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * The Initial Developer of the Original Code is Michael Phillips <michael.j.phillips@gmail.com>.
 * 
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *  
 * Contributor(s): 
 *  
 *
 */
/**
 * voicemail.php - Sofia Viewer Module
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package FreePBX3
 * @subpackage Voicemail
 * @class Voicemail_Controller
 * @extends FreePbx_Controller
 * @todo Finish archive function
 */
class VoicemailViewer_Controller extends FreePbx_Controller
{
	public function index($folder = 'inbox')
    {
		$this->view->allMessages = array();
		$this->view->messageCount = '';
		
	
		$vm = VoicemailManager::init();
		
		if(sizeof($this->input->post()) != 0)
		{
			switch($this->input->post('action'))
			{
				case 'mark_read':
					$vm->markMessageRead($this->input->post('uuid')); 
				break;	
				
				case 'mark_unread':
					$vm->markMessageUnread($this->input->post('uuid'));
				break;
				
				case 'mark_urgent_read':
					$vm->markMessageRead($this->input->post('uuid'), true);
				break;			

				case 'mark_urgent_unread':
					$vm->markMessageUnread($this->input->post('uuid'), true);
				break;	
				
				case 'delete':
					$vm->delete($this->input->post('uuid'));
				break;
				
				case 'archive':
					//$this->archive(VoicemailManager::getPath($uuid), 'Archived Voicemail', $this->session->get('user_id'));		
				break;
			
			}
		}
		
		$this->view->newMessages = 0;
		$this->view->savedMessages = 0;
		$this->view->newUrgentMessages = 0;
		$this->view->savedUrgentMessages = 0;	
		
		foreach($vm->getDevices($this->session->get('user_id')) as $devices)
		{
			$device_id = $devices['device_id'];

			$vm->MessageCount($device_id, 
				$this->view->newMessages, 
				$this->view->savedMessages, 
				$this->view->newUrgentMessages, 
				$this->view->savedUrgentMessages);
			
			$this->view->messageCount .= "<tr class=\"txt-center\">
			<td>" . sipdevices::getUsername($device_id) ."</td>
			<td>{$this->view->newMessages}</td>
			<td>{$this->view->savedMessages}</td>
			<td>{$this->view->newUrgentMessages}</td>
			<td>{$this->view->savedUrgentMessages}</td>
			</tr>";
			
			//$vm->updateMWI($device_id);
			
		}
		$this->view->allMessages = $vm->getAllMessages($_SESSION['user_id']);
	}

	public function messages($folder)
    {
        $this->template->title = "Voicemail";
		
		
    }	
	
	public function listen($uuid)
	{
		$vm = VoicemailManager::init();
		$file = $vm->getPath($uuid);
		//$vm->markMessageRead($uuid);
		header("Content-type: audio/wav");
		header('Content-Length: '.filesize($file)); 
		readfile($file);		
		die();
	}

	public function download($uuid = null)
	{
		$vm = VoicemailManager::init();
		$file = $vm->getPath($uuid);
		$filename = $vm->getDownloadFilename($uuid);
		header("Content-type: audio/wav");
		header('Content-Disposition: attachment; filename="' . $filename . '.wav"');
		header('Content-Length: '.filesize($file)); 
		readfile($file);
		die();			
	}
	
	private function archive($path, $description = '', $user_id = null)
	{
		$name = basename($path);
		
		$file = new File();
		$file->name = $name;
		$file->description = $description;
		$file->type = 'audio/wav'; //file::mime($name); //conflicts with File.  Damn, can I get a namespace
		$file->size = filesize($path);
		$file->user_id = $user_id;
		$file->save();
		copy($path, Kohana::config('upload.directory') . "/" . $user_id . "/". $name);
	}
	
	public function settings($number_id = 0)
	{
		if(sizeof($this->input->post()) > 0)
		{
			$vm = VoicemailManager::init();
			$vm->setPassword($this->input->post('password'), $number_id);
			$message = "Updated Password";
			
		}
	}
	
	/**
	 * Used for AJAX requests to set the status of a message
	 */

	public function status()
	{
		$status = $this->input->post('status');
		$uuid = $this->input->post('uuid');
		$vm = VoicemailManager::init();
		switch($status)
		{
			case 'mark_read':
				$vm->markMessageRead($uuid);
			break;
			
			case 'mark_unread':
				$vm->markMessageUnead($uuid);
			break;			
		}
	}
	
	/**
	 * 
	 *  blast
	 * 	<user>@<domain> <sound_file> [<cid_num>] [<cid_name>]
	 *  voicemail_inject is used to add an arbitrary sound file to a users voicemail mailbox. 
	 */ 
	public function blast()
	{

            $numbers = array();
            //var_dump(sipdevices::getSipNumbers());
            foreach(sipdevices::getSipNumbers() as $number)
            {
                    $numbers[$number['device_id']]=  $number['Sip']['username'] . '@' . $number['User']['Location']['domain'];
            }
            $this->view->numbers = $numbers;
            $this->view->endpoints = sipdevices::getSipEndpoints();
            
            if($this->input->post()) {
                    if($this->input->post('file_id') == 0)  {
                            message::set('Select a file to to use in the voicemail message');
                       } else {
                            foreach($this->input->post('blast') as $device_id) {
				$vm = VoicemailManager::init();
				$vm->injectVoicemail($this->input->post('file_id'), $device_id);
                            }
                      }
		}
	}
}
