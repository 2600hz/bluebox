<?php
class VoicemailManager
{
	public static function getCount($mailbox, $domain)
	{
		$eslManager = new EslManager();

                $cmd = sprintf('vm_boxcount %s@%s|all', $mailbox, $domain);
                Kohana::log('info','ESL: ' . $cmd);

		$count = $eslManager->getResponse($eslManager->api($cmd));
		$count = explode(':', $count);

                if(sizeof($count) != 4)  {
                    $count = array('new' => 'No Mailbox Defined!', 'saved' => '', 'new-urgent' => '', 'saved-urgent' => '');
                } else {
                    $count = array('new' => $count[0], 'saved' => $count[1], 'new-urgent' => $count[2], 'saved-urgent' => $count[3]);
                }

		return $count;
	
	}
	

	public static function getList($user, $domain)
	{

		//@TODO 10 second cache?
		$eslManager = new EslManager();
		$cmd = sprintf('vm_list %s@%s xml', $user, $domain);
                
                Kohana::log('info','ESL: ' . $cmd);
                
                $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" . $eslManager->getResponse($eslManager->api($cmd));

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

			$voicemails[(string)$voicemail->uuid] = $tmp;
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
	
	public static function getMailboxes($account_id, $user_id = NULL)
	{
		$tmp = array();
		
		$mailboxes = Doctrine::getTable('Voicemail')->findByAccountId($account_id);
		foreach($mailboxes as $mailbox)
		{
			$tmp[$mailbox->mailbox] = $mailbox->mailbox;
		}
		return $tmp;
	}

        public static function getAllMailboxes()
        {
            $mailboxes = Doctrine::getTable('Voicemail')->findAll();
            $tmp = array();
            foreach($mailboxes as $mailbox)
               {
                $tmp[$mailbox->mailbox] = $mailbox->mailbox;
            }
            return $tmp;
        }

        public static function getDomain($account_id = NULL)
        {
            return 'voicemail_1';
        }

        public static function blast($mailbox, $domain, $file)
        {
            //voicemail_inject,[group=]<box> <sound_file> [<cid_num>] [<cid_name>],voicemail_inject,mod_voicemail
                
		$inject = sprintf('voicemail_inject %s@%s %s %s %s', $mailbox, $domain, $file, '4000', 'Voicemail');
                
		$eslManager = new EslManager();
		$result = $eslManager->getResponse($eslManager->api($inject));
                return $result;
        }

        public static function delete($user, $domain, $uuid)
	{
		//vm_delete,<id>@<domain>[/profile] [<uuid>],vm_delete,mod_voicemail
		$eslManager = new EslManager();
		$eslManager->getResponse($eslManager->api(sprintf('vm_delete %s@%s %s', $user, $domain, $uuid)));
	}
}
