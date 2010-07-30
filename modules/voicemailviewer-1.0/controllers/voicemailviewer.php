<?php

class VoicemailViewer_Controller extends Bluebox_Controller {
    public $domain;

    public function index() {
        $user = users::$user;
        /* setup account vars */
        $user_id = $user->_data['user_id'];
        $location_id = $user->_data['location_id'];
        $account_id = $user->_data['account_id'];
        $user_id = $user->_data['user_id'];

        $domain = 'voicemail_1';



        $this->template->content = new View('voicemailviewer/index');

        

        $mailboxes = VoicemailManager::getMailboxes($account_id);

        foreach($mailboxes as $mailbox) {
            
            $this->template->content->count = VoicemailManager::getCount($mailbox, $domain);
            $list = VoicemailManager::getList($mailbox, $domain);
            $this->template->content->list = $this->showMessages($list);
        }
    }



    public function listen($uuid) {
        $vm = VoicemailManager::init();
        $file = $vm->getPath($uuid);
        //$vm->markMessageRead($uuid);
        header("Content-type: audio/wav");
        header('Content-Length: '.filesize($file));
        readfile($file);
        die();
    }

    public function download($uuid = null) {
        $vm = VoicemailManager::init();
        $file = $vm->getPath($uuid);
        $filename = $vm->getDownloadFilename($uuid);
        header("Content-type: audio/wav");
        header('Content-Disposition: attachment; filename="' . $filename . '.wav"');
        header('Content-Length: '.filesize($file));
        readfile($file);
        die();
    }

    private function archive($path, $description = '', $user_id = null) {

    }

    private function showMessages($list) {

        $html = '<table width="100%">';
        //$idx = array('created_epoch', 'read_epoch', 'username', 'domain', 'path', 'uuid', 'cid-name', 'cid-number');
        $html .= '<tr><th>Received</th><th>Name</th><th>Number</th></tr>';
        foreach($list as $message) {
            $html .= '<tr>';
            $html .= '<td>' . date('h:i:s a m/d/Y', (int)$message['created_epoch']) .'</td>';
            $html .= '<td>' . $message['cid-name'] .'</td>';
            $html .= '<td>' . $message['cid-number'] .'</td>';
            $html .= '</tr>';
        }

        $html .= '<table>';

        return $html;
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

}
