<?php
/**
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @class VoicemailManager
 * @todo Add a get device id by uuid function which may not be feasible. FS creates the schema and where is no real primary to setup a relationship on
 * @todo look for better way to do MWI updates
 * @todo move functions that can be used by more than voicemail into numbers maybe
 * 
 * Hopfully FS will introduce ESL api calls do the direct SQL for message updates and deletes 
 * can go away.
 */

/**
 * Some defines 
 * 
 * For urgent and normal messages not sure if this concept
 * applies to multiple engines/pbxs. might not be the best place for the 
 * define...
 */
define("URGENT_FLAG_STRING", "A_URGENT");
define("NORMAL_FLAG_STRING", "B_NORMAL");

class VoicemailManager
{
	private static $instance 	= null; 	// Singleton instance
	private static $username 	= null; 	// Username part of the sip address
	private static $domain 		= null; 	// Domain of the sip address, i.e. freepbx.org
	private static $device_id 	= null; 	// Device id.
	private $esl 	= null; 	// Device id.
	
	public static function init($driver = 'odbc') //driver is odbc only for now, this parameter is ignored
	{
		if (!isset(self::$instance)) 
		{
			$class = __CLASS__;
			self::$instance = new $class;
		}
		
		self::$instance->esl = new EslManager();
		

		return self::$instance;
	}	
	
	/**
	 * Set the current message id (uuid) but also set the domain and username for a possible mwi call
	 * @param string @uuid
         * @todo deprecate this
	 */
	
	public static function setMessageId($uuid)
	{
		$message = Doctrine::getTable('VoicemailMessage')->find($uuid);
		self::$username = $message->username;
		self::$domain  = $message->domain;
	}
	
	/**
	 * Set the current device id ut also set the domain and username for a possible mwi call
	 * @param int $device_id
	 */
	
	public static function setDeviceId($device_id)
	{
		self::$username = sipdevices::getUserName($device_id);
		self::$domain = sipdevices::getDomain($device_id);
		self::$device_id = $device_id;
	}
	
	/**
	  Get all the device_ids for a user_id
	 * @param int user_id
	 * @return array All devices owned by the user
	 */
	public function getDevices($user_id = 0)
	{
		$q = Doctrine_Query::create()->select('d.device_id')->from('Device d')->where('user_id = ?', $user_id);
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $result;
	}

	/**
	 * Get all the messages for a device
	 */
	public function getAllMessages($user_id)
	{
		
		$devices = VoicemailManager::getDevices($_SESSION['user_id']);

		$q = Doctrine_Query::create()
			->select('vm.username,
			vm.domain,
			vm.created_epoch, 
			vm.read_epoch, 
			vm.uuid, 
			vm.cid_name, 
			vm.cid_number, 
			vm.in_folder, 
			vm.file_path, 
			vm.message_len')
			->from('VoicemailMessage vm');
			
		foreach($devices as $device_id)
		{
			$username = sipdevices::getUserName($device_id);
			$domain = sipdevices::getDomain($device_id);
			$q->orWhere('username = ? AND domain = ?', array($username, $domain));
		}
			
		$q->orderBy('created_epoch desc');
		//var_dump($q->getSqlQuery());
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $result;
	}
	
	public function getNewMessages($device_id)
	{
		$username = sipdevices::getUserName($device_id);
		$domain = sipdevices::getDomain($device_id);
		
		$q = Doctrine_Query::create()
			->select('vm.username,
			vm.domain,
			vm.created_epoch, 
			vm.read_epoch, 
			vm.uuid, 
			vm.cid_name, 
			vm.cid_number, 
			vm.in_folder, 
			vm.file_path, 
			vm.message_len')
			->from('VoicemailMessage vm')
			->where('vm.read = 0')
			->andWhere('username = ?', $username)
			->andWhere('domain = ?', $domain)
			->orderBy('created_epoch desc');
		
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $result;		
	}

	/**
	 * Return all folders owned by the user as an indexed array.
	 * 
	 * @access public 
	 * @static
	 * @param string username
	 * @return array
	 */

	public static function getFolders($device_id)
	{
		$username = sipdevices::getUserName($device_id);
		$domain = sipdevices::getDomain($device_id);
			
		$q = Doctrine_Query::create()
		->select('vm.in_folder')
		->from('VoicemailMessage vm')
		->groupBy('vm.in_folder')
                ->where('username = ? ', array($username))
                ->andWhere('domain = ? ', array($domain));
		
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $result;
	}
	
	public static function getMessageStatus($uuid, $param = '')
	{
		$q = Doctrine_Query::create()
			->select('vm.created_epoch, 
			vm.read_epoch, 
			vm.flags, 
			vm.read_flags')
			->from('VoicemailMessage vm')
			->where('vm.uuid = ?', $uuid);
		$result = $q->fetchOne(array(), Doctrine::HYDRATE_ARRAY); //needed for sqlite support

		if($result['read_flags'] == URGENT_FLAG_STRING && $result['read_epoch'] == 0) { 
			return '<img src="' . url::base() . 'modules/voicemail/assets/urgent.png" border="0" />';	
		} elseif($result['read_flags'] == URGENT_FLAG_STRING && $result['flags'] == 'save') {
			return '<img src="' . url::base() . 'modules/voicemail/assets/saved_urgent.png" border="0" />';	
		} elseif($result['read_flags'] == NORMAL_FLAG_STRING && $result['flags'] == 'save') {
			return '<img src="' . url::base() . 'modules/voicemail/assets/saved.png" border="0" />';
		} elseif ($result['read_flags'] == NORMAL_FLAG_STRING && $result['read_epoch'] == 0) {
			return '<img src="' . url::base() . 'modules/voicemail/assets/new.png" border="0" />';
		} elseif ($result['read_flags'] == 'B' && $result['read_epoch'] == 0) {
			return '<img src="' . url::base() . 'modules/voicemail/assets/new.png" border="0" />';
		} else {
			return '';
		}
	}	

	
	/************ Mark Message, Delete and Methods ************/
	
	
	/**
	 * Return the high level status of a message (new, saved, urgent)
	 * 
	 * @param string $uuid
	 * @return string 
	 */

	public function markMessageUnread($uuid, $urgent = false)
	{
		$result = false;
		if(!is_null($uuid))
		{
			if(!is_array($uuid))
			{
				$uuid = array($uuid); //convert to array if just a string
			}
			
			foreach($uuid as $msg)
			{
				$read_flags = ($urgent) ? URGENT_FLAG_STRING : NORMAL_FLAG_STRING;
				$q = Doctrine_Query::create()
					->update('VoicemailMessage vm')
					->set('vm.read_epoch', '?', 0)
					->set('vm.flags', '?',  '')
					->set('vm.read_flags', '?', $read_flags)
					->where('vm.uuid = ?', $msg);
					
				$result = $q->execute();
			}
		}
		return self::$instance;
	}
	/**
	 * 
	 * @param string $uuid
	 * @param bool $urgent 
	 */
	
	public function markMessageRead($uuid, $urgent = false)
	{
		$result = false;
		if(!is_null($uuid))
		{
			if(!is_array($uuid))
			{
				$uuid = array($uuid); //convert to array if just a string
			}
			
			foreach($uuid as $msg)
			{
				$read_flags = ($urgent) ? URGENT_FLAG_STRING : NORMAL_FLAG_STRING;
				$q = Doctrine_Query::create()
					->update('VoicemailMessage vm')
					->set('vm.read_epoch', '?', time())
					->set('vm.flags', '?',  'save')
					->set('vm.read_flags', '?',  $read_flags)
					->where('vm.uuid = ?',  $msg);
				$result = $q->execute();
			}
		}
		return self::$instance;
	}

	/**
	 * Delete a message based on the uuid and update the MWI on the endpoint
	 * device. Delete the message from the filesystem and update the
	 * database.
	 * 
	 * @todo ESL call
	 * @access public 
	 * @static
	 * @param string uuid
	 * @return bool
	 */
	
	public function delete($uuid) 
	{
		$result = true;
		$msg = '';  //set this here so it lives outside the scope of the foreach and we only have to call upateMWI once
		if(!is_null($uuid))
		{
				
			foreach($uuid as $msg)
			{
				$file = self::getPath($msg);
				if(file_exists($file) && @unlink($file))
				{
					$q = Doctrine_Query::create()
					->delete('VoicemailMessage vm')
					->where('vm.uuid = ?', $msg);
					$result = $result & $q->execute(); //that is supposed to be a bitwise operator
				} else {
					message::set('The web service is unable to remove the file: ' . $file);
					$result = $result & false;
				}
			}
		}	
		return $result;
	}
	
	/**
	 * Get the absolute path to the voicemail message on the filesystem
	 * based on the uuid.
	 * 
	 * @access public 
	 * @static
	 * @param sting uuid
	 */
	
	public function getPath($uuid)
	{
		$q = Doctrine_Query::create()
		->select('vm.file_path')
		->from('VoicemailMessage vm')
		->where('vm.uuid = ?', $uuid);
		$result = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
		return $result;
	}
	
	/**
	 * Genereate a filename for downloads have has some information about
	 * the message, like the name, number and date. Replace spaces with
	 * underscores.
	 * 
	 * @access public
	 * @static
	 * @param string uuid
	 * @return string 
	 */
	
	public function getDownloadFilename($uuid)
	{
		$q = Doctrine_Query::create()
		->select('vm.cid_name, vm.cid_number')
		->from('VoicemailMessage vm')
		->where('vm.uuid = ?', $uuid);
		
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		$result = str_replace(' ', '_', $result[0]['cid_name'] . ' ' . $result[0]['cid_number'] . ' ' . date('M d Y'));
		return $result;
	}
	
	/**
	 * Update the MWI on the endpoint device
	 * @access public 
	 * @static
	 * @param integer device_id
	 * @return void
	 * @todo We need to figure out what we are going to require as parameters
	 */

	public function updateMWI($device_id)
	{
		$username = self::$username;
		$domain = self::$domain;

		$yn = "no";

		$newMessages = 0;
		$savedMessages = 0;
		$newUrgentMessages = 0;
		$savedUrgentMessages = 0;	

		$this->MessageCount($device_id, $newMessages, $savedMessages, $newUrgentMessages, $savedUrgentMessages);
 		
		if (($newMessages > 0) || ($newUrgentMessages > 0)) 
		{
                $yn = "yes";
        }

		
		if($this->esl->isConnected())
		{
			$event = new ESLevent('MESSAGE_WAITING');
			//$event->addHeader('Sofia-Profile', 'internal');
			$event->addHeader('MWI-Messages-Waiting',sprintf('%s',$yn)); // (yes/no)
			$event->addHeader('MWI-Message-Account', sprintf('%s@%s', $username, $domain));
			$event->addHeader('MWI-Voice-Message',sprintf('%d/%d (%d/%d)', $newMessages, $savedMessages, $newUrgentMessages, $savedUrgentMessages));
			
			$response = $this->esl->sendEvent($event);
			
		} else {
			
			return false; //I love Arby's
		}
	}
	

	/**
	 * Get message count of folder
	 * @access public 
	 * @static
	 * @param string username
	 * @param int newMessages
	 * @param int savedMessages
	 * @param int newUrgentMessages
	 * @param int savedurgentMessages
	 * @param string folder
	 * @return void
	 */ 

	public function MessageCount($device_id, &$newMessages, &$savedMessages, &$newUrgentMessages, &$savedUrgentMessages, $folder = 'inbox')
	{
		$username = sipdevices::getUserName($device_id);
		$domain = sipdevices::getDomain($device_id);
		if($this->esl->isConnected())
		{
			//<user>@<domain> [|[new|saved|new-urgent|saved-urgent|all]]
			$response =  $this->esl->sendAPI('vm_boxcount', sprintf("%s@%s|all", $username, $domain));
			
			$result = explode(':', $response);
			
			if(sizeof($result) != 4)
			{
				//message::set(__('vm_boxcount api call returned wrong data: ' . $response . '.  This could be that ESL is not working'));
				$result = array(0,0,0,0);	
			}
			
			$newMessages = $result[0];
			$savedMessages = $result[1];
			$newUrgentMessages = $result[2];
			$savedUrgentMessages = $result[3];
			
		} else {
			
			$newMessages = 0;
			$savedMessages = 0;
			$newUrgentMessages = 0;
			$savedUrgentMessages = 0;
		}
	}
	
	/**
	 * Put audio file directly in user's voicemail "box"
	 * 
	 * @param int file_id
	 * @param int device_id
	 * @return mixed bool|string
	 */
	public function injectVoicemail($file_id, $device_id)
	{
		$username = sipdevices::getUserName($device_id);
		$domain = sipdevices::getDomain($device_id);
		$file = FileManager::getFilePath($file_id);
		/**
		 * 	<user>@<domain> <sound_file> [<cid_num>] [<cid_name>]
		 *  voicemail_inject is used to add an arbitrary sound file to a users voicemail mailbox. 
		 */
		$inject = sprintf('%s@%s %s %d %s', $username, $domain, $file, '0000000000', 'System Voicemail');

		if($this->esl->isConnected())
		{
			return $this->esl->sendBGAPI('voicemail_inject', $inject );
		} else {
			return false;
		}	
	}	

	/**
	 * Pretty Date get google type information on how long ago a message
	 * was left.  For exmaple, "Just now" or "40 minutes ago"
	 */

	public static function prettyDate($time, $unixtime = false){
		if(!$unixtime)
		{
			$time = strtotime($time);
		}
		$currentTime = time();
		$secondDiff = $currentTime - $time;
		$dayDiff = floor($secondDiff / 86400);

		if($secondDiff  < 60)
			return "just now";

		if($secondDiff  < 60)
			return "1 minute ago";

		if($secondDiff < 3600)
			return floor( $secondDiff / 60 ) . " minutes ago";

		if($secondDiff <7200)
			return "1 hour ago";

		if($secondDiff <86400 ) 
			return floor( $secondDiff / 3600 ) . " hours ago";

		if($dayDiff == 1)
			return "Yesterday";

		if($dayDiff < 7)
			return $dayDiff . " days ago";

		if($dayDiff <31)
			return floor( $dayDiff / 7 ) . " weeks ago";

		if($dayDiff <365)
			return floor( $dayDiff / 30 ) . " months ago";
	}
}
