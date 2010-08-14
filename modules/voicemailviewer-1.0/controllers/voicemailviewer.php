<?php

class VoicemailViewer_Controller extends Bluebox_Controller {
  public $domain;
  protected $authBypass = array('service');

  public $baseModel = 'VoicemailMessage';

  public function index() {
    $user = users::$user;
    /* setup account vars */
    $user_id = $user->_data['user_id'];
    $location_id = $user->_data['location_id'];
    $account_id = $user->_data['account_id'];
    $user_id = $user->_data['user_id'];

    $domain = 'voicemail_1';

    $mailboxes = VoicemailManager::getMailboxes($account_id);

    $vms = array();
    $list = array();

    // ghetto multibox support
    foreach ( $mailboxes as $mailbox ) {
      VoicemailManager::updateVMTables($mailbox, $domain);
    }

    $this->template->content = new View('voicemailviewer/index');

    $vms_grid = jgrid::grid('VoicemailOverview', array('caption' => 'Overview'));
    $vms_grid
      ->add('username', 'Mailbox')
      ->add('domain', 'Domain')
      ->add('new', 'New', array('align' => 'center'))
      ->add('saved', 'Saved', array('align' => 'center'))
      ->add('new_urgent', 'Urgent New', array('align' => 'center'))
      ->add('saved_urgent', 'Urgent Saved', array('align' => 'center'));

    $list_grid = jgrid::grid($this->baseModel, array('caption' => 'Voicemails'));
    $list_grid->add('created_epoch', 'Created', array('callback' => array(
									  'function' => array($this, '_formatCreatedEpoch')
									  ,'arguments' =>  array('created_epoch')
									  )
						      )
		    )
      ->add('username', 'Username')
      ->add('cid_name', 'Caller ID Name')
      ->add('cid_number', 'Caller ID Number')
      ->add('listen', 'Listen', array('callback' => array(
							  'function' => array($this, '_createAudioTag')
							  ,'arguments' => array('uuid')
							  )
				      )
	    )
      ->addAction('voicemailviewer/download', 'Download', array('arguments' => 'uuid'))
      ->addAction('voicemailviewer/delete', 'Delete', array('arguments' => 'uuid'));

    $this->template->content->vms_grid = $vms_grid->produce();
    $this->template->content->list_grid = $list_grid->produce();
  }

  public function _formatCreatedEpoch($null, $time) {
    if ( ! strcmp(date('Ymd'), date('Ymd', $time)) ) {
      return 'Today at ' . date('g:i:s a', $time);
    }

    return date('Y-m-d H:i:s', $time);
  }

  public function _createAudioTag($null, $uuid) {
    $listenURL = url::site('/voicemailviewer/listen/'. $uuid);
    return '<audio controls="controls" preload="none" src="' . $listenURL . '">Install FireFox or Chrome</audio>';
  }

  public function delete($uuid) {
    $base = strtolower($this->baseModel);

    $this->createView();

    $this->loadBaseModel($uuid);

    if ($action = $this->submitted(array('submitString' => 'delete'))) {
      Event::run('bluebox.deleteOnSubmit', $action);

      if ( ($action == self::SUBMIT_CONFIRM) ) {
	if ( ($msg = Doctrine::getTable('VoicemailMessage')->findOneByUuid($uuid)) === NULL ) {
	  messge::set('Unable to delete voicemail message.');
	  $this->exitQtipAjaxForm();
	  url::redirect(Router_Core::$controller);
	} else {
	  $domain = $msg->domain;
	  $username = $msg->username;
	  VoicemailManager::delete($username, $domain, $uuid);
	  $msg->delete();
	}

	$this->returnQtipAjaxForm(NULL);

	url::redirect(Router_Core::$controller);   
      } else if ($action == self::SUBMIT_DENY) {
	$this->exitQtipAjaxForm();

	url::redirect(Router_Core::$controller);
      }
    }

    $this->prepareDeleteView(NULL, $uuid);
  }

  public function listen($uuid) {
    $this->auto_render = FALSE;

    if ( ($msg = Doctrine::getTable('VoicemailMessage')->findOneByUuid($uuid, Doctrine::HYDRATE_ARRAY)) === NULL ) {
      messge::set('Unable to find voicemail message.');
      url::redirect(url::site('voicemailviewer'));
    }

    $file = $msg['file_path'];

    if ( ! file_exists($file) ) {
      Kohana::log('error', 'Can\'t access file: '  . $file);
      return;
    }

    header("Content-type: audio/wav");
    header('Content-Length: '.filesize($file));
    readfile($file);
    die();
  }

  public function download($uuid) {
    $this->auto_render = FALSE;

    if ( ($msg = Doctrine::getTable('VoicemailMessage')->findOneByUuid($uuid, Doctrine::HYDRATE_ARRAY)) === NULL ) {
      messge::set('Unable to find voicemail message.');
      url::redirect(url::site('voicemailviewer'));
    }

    $file = $msg['file_path'];

    if ( ! file_exists($file) ) {
      Kohana::log('error', 'Can\'t access file: '  . $file);
      return;
    }

    header("Content-type: audio/wav");
    header('Content-Disposition: attachment; filename="voicemail.wav"');
    header('Content-Length: '.filesize($file));
    readfile($file);
    die();
  }

    /**
     *
     *  blast
     * 	<user>@<domain> <sound_file> [<cid_num>] [<cid_name>]
     *  voicemail_inject is used to add an arbitrary sound file to a users voicemail mailbox.
     */
    public function blast() {
        $domain = VoicemailManager::getDomain();


        $this->view->mailboxes =VoicemailManager::getAllMailboxes();

        if($this->input->post()) {
            if($this->input->post('file_id') == 0) {
                message::set('Select a file to to use in the voicemail message');
            } else {
                foreach($this->input->post('blast') as $mailbox) {

                    echo VoicemailManager::blast($mailbox, $domain, Media::getFilePath($this->input->post('file_id')));
                }
            }
        }
    }

    public function service($key = NULL)
    {
        Kohana::log('info', 'Incoming email');

    }

}
