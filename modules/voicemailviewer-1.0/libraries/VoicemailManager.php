<?php
class VoicemailManager
{
	public static function getCount($user, $domain)
	{
		$eslManager = new EslManager();
		$count = $eslManager->getResponse($eslManager->api(sprintf('vm_boxcount %s@%s|all', $user, $domain)));
		$count = explode(':', $count);
		$count = array('new' => $count[0], 'saved' => $count[1], 'new-urgent' => $count[2], 'saved-urgent' => $count[3]);
		return $count;
	
	}
	
	public static function getList($user, $domain)
	{
		
		$eslManager = new EslManager();
		$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" . $eslManager->getResponse($eslManager->api(sprintf('vm_list %s@%s xml', $user, $domain)));
		
		$idx = array('created_epoch', 'read_epoch', 'username', 'domain', 'path', 'uuid', 'cid-name', 'cid-number');

		$xml = simplexml_load_string($xml);
	
		$voicemails = array();
	
		foreach($xml as $voicemail)
		{
			$tmp = array();
			
			foreach($idx as $header)
			{
				$tmp[$header] = $voicemail->$header;
			}
			
			$voicemails[] = $tmp;
		}
		return $voicemails;
		
	}
	
	public static function markRead()
	{
		//vm_read,<id>@<domain>[/profile] <read|unread> [<uuid>],vm_read,mod_voicemail
	}
	
	public static function markUnread()
	{
		//vm_read,<id>@<domain>[/profile] <read|unread> [<uuid>],vm_read,mod_voicemail
	}
}
