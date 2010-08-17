<?php

define('VM_BOXCOUNT_EXPECTED', 4);
define('VM_BOXCOUNT_NEW', 0);
define('VM_BOXCOUNT_SAVED', 1);
define('VM_BOXCOUNT_NEW_URGENT', 2);
define('VM_BOXCOUNT_SAVED_URGENT', 3);

class VoicemailManager {
  public static function getCount($mailbox, $domain) {
    $eslManager = new EslManager();

    $cmd = sprintf('vm_boxcount %s@%s|all', $mailbox, $domain);

    $call = $eslManager->api($cmd);
    $resp = $eslManager->getResponse($call);
    $fields = explode(':', $resp);

    if ( count($fields) !== VM_BOXCOUNT_EXPECTED ) {
      kohana::log('error', 'ESL: Cmd ' . $cmd . ' failed to execute in an expected way. Result: ' . $resp);
      return NULL;
    }

    return array('new' => $fields[VM_BOXCOUNT_NEW]
		 ,'saved' => $fields[VM_BOXCOUNT_SAVED]
		 ,'new-urgent' => $fields[VM_BOXCOUNT_NEW_URGENT]
		 ,'saved-urgent' => $fields[VM_BOXCOUNT_SAVED_URGENT]
		 );
  }

  public static function updateVMTables($user, $domain) {
    $eslManager = new EslManager();
    $cmd = sprintf('vm_list %s@%s xml', $user, $domain);

    $call = $eslManager->api($cmd);
    $resp = $eslManager->getResponse($call);

    $xml = @simplexml_load_string("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" . $resp);

    if ( $xml === FALSE ) {
      kohana::log('error', 'ESL: Cmd ' . $cmd . ' failed to execute in an expected way. Result: ' . $resp);
      throw new Exception($resp);
    }

    $vm = new VoicemailMessage();

    foreach ( $xml as $voicemail ) {
      $v = (array) $voicemail;
      $v['cid_name'] = $v['cid-name'];
      $v['cid_number'] = $v['cid-number'];
      $v['in_folder'] = $v['folder'];
      $v['file_path'] = $v['path'];

      $vm->fromArray($v);
      $vm->replace();
    }

    $cmd = sprintf('vm_boxcount %s@%s|all', $user, $domain);

    $call = $eslManager->api($cmd);
    $resp = $eslManager->getResponse($call);
    $fields = explode(':', $resp);

    if ( count($fields) !== VM_BOXCOUNT_EXPECTED ) {
      kohana::log('error', 'ESL: Cmd ' . $cmd . ' failed to execute in an expected way. Result: ' . $resp);
      throw new Exception($resp);
    }

    $overview = new VoicemailOverview();
    $overview->fromArray(array('new' => $fields[VM_BOXCOUNT_NEW]
			       ,'saved' => $fields[VM_BOXCOUNT_SAVED]
			       ,'new_urgent' => $fields[VM_BOXCOUNT_NEW_URGENT]
			       ,'saved_urgent' => $fields[VM_BOXCOUNT_SAVED_URGENT]
			       ,'username' => $user
			       ,'domain' => $domain
			       ));
    $overview->replace();
  }

  public static function getList($user, $domain) {
    //@TODO 10 second cache?

    //@TODO Put this in a cron
    self::updateVMTables($user, $domain);

    $eslManager = new EslManager();
    $cmd = sprintf('vm_list %s@%s xml', $user, $domain);

    $call = $eslManager->api($cmd);
    $resp = $eslManager->getResponse($call);

    kohana::log('debug', 'ESL: Cmd ' . $cmd . ' failed to execute in an expected way. Result: ' . $resp);

    $xml = simplexml_load_string("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" . $resp);
	
    $voicemails = array();

    foreach ( $xml as $voicemail ) {
      $voicemails[] = (array) $voicemail;
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
	
  public static function getMailboxes($account_id, $user_id = NULL) {
    $q = Doctrine_Query::create()
      ->select('v.mailbox')
      ->from('Voicemail v')
      ->where('v.account_id = ?');
    return $q->execute($account_id, Doctrine::HYDRATE_ARRAY);
  }

  public static function getAllMailboxes() {
    $q = Doctrine_Query::create()
      ->select('v.mailbox')
      ->from('Voicemail v');
    $boxes = $q->execute(NULL, Doctrine::HYDRATE_ARRAY);
    return array_map('vm_get_mailbox', $boxes);
  }

  public static function getDomain($account_id = NULL) {
    return 'voicemail_1';
  }

  public static function blast($mailbox, $domain, $file) {
    //voicemail_inject,[group=]<box> <sound_file> [<cid_num>] [<cid_name>],voicemail_inject,mod_voicemail

    $inject = sprintf('voicemail_inject %s@%s %s %s %s', $mailbox, $domain, $file, '4000', 'Voicemail');

    $eslManager = new EslManager();
    $result = $eslManager->getResponse($eslManager->api($inject));
    return $result;
  }

  public static function delete($user, $domain, $uuid) {
    //vm_delete,<id>@<domain>[/profile] [<uuid>],vm_delete,mod_voicemail
    $eslManager = new EslManager();
    $resp = $eslManager->getResponse($eslManager->api(sprintf('vm_delete %s@%s %s', $user, $domain, $uuid)));
    kohana::log('debug', 'VM Delete returned ' . $resp);
  }
}

function vm_get_mailbox($data) {
  return $data['mailbox'];
}