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

        $count = '';
        $list = '';
        // ghetto multibox support
        foreach($mailboxes as $mailbox) {

            $count  .= $this->showCount(VoicemailManager::getCount($mailbox, $domain));

            $list .= $this->showMessages(VoicemailManager::getList($mailbox, $domain));
        }
        $this->template->content->count = $count;
        $this->template->content->list = $list;

    }

    public function delete($domain, $mailbox, $uuid) {
        $this->auto_render = FALSE;

        $domain = 'voicemail_1';
        $voicemails = VoicemailManager::delete($mailbox, $domain, $uuid);
        url::redirect(url::site('voicemailviewer'));
    }

    public function listen($domain, $mailbox, $uuid) {
        $this->auto_render = FALSE;

        $voicemails = VoicemailManager::getList($mailbox, $domain);
        $file = $voicemails[$uuid]['path'];

        header("Content-type: audio/wav");
        header('Content-Length: '.filesize($file));
        readfile($file);
        die();
    }

    public function download($domain, $mailbox, $uuid) {
        $this->auto_render = FALSE;

        $voicemails = VoicemailManager::getList($mailbox, $domain);
        $file = $voicemails[$uuid]['path'];

        header("Content-type: audio/wav");
        header('Content-Disposition: attachment; filename="voicemail.wav"');
        header('Content-Length: '.filesize($file));
        readfile($file);
        die();
    }

    private function showMessages($list) {

       $html = '<table width="100%" class="ui-widget ui-jqgrid">';
        //$idx = array('created_epoch', 'read_epoch', 'username', 'domain', 'path', 'uuid', 'cid-name', 'cid-number');
        $html .= '<tr><th>Received</th><th>Mailbox</th><th>Caller Name</th><th>Caller Number</th><th>Actions</th></tr>';
        foreach($list as $message) {
            $listenURL = url::site('/voicemailviewer/listen/'. $message['domain'].'/'.$message['username']) . '/';
            $deleteURL = url::site('/voicemailviewer/delete/'. $message['domain'].'/'.$message['username']) . '/';
            $downloadURL = url::site('/voicemailviewer/download/'. $message['domain'].'/'.$message['username']) . '/';


            $html .= '<tr id="message_' . $message['uuid'] . '">';
                $html .= '<td>' . date('h:i:s a m/d/Y', (int)$message['created_epoch']) .'</td>';
                $html .= '<td>'. $message['username'] . '</td>';
                $html .= '<td>' . $message['cid-name'] .'</td>';
                $html .= '<td>' . $message['cid-number'] .'</td>';
                $html .= '<td>' . html::anchor($deleteURL . $message['uuid'], 'Delete') .' |  ' . html::anchor($downloadURL . $message['uuid'], 'Download') . '</td>';
            $html .= '</tr>';
            ;
            $html.= '<tr><td>&nbsp;</td><td colspan="4"><audio controls="controls" preload="none" src="' . $listenURL . $message['uuid'] . '">Install FireFox or Chrome</audio></td></tr>';
        }

        $html .= '<table>';

        return $html;
    }

    public function showCount($count) {
        $html = "<ul>
            <li>New: {$count['new']}</li>
            <li>Saved: {$count['saved']}</li>
            <li>Urgent New: {$count['new-urgent']}</li>
            <li>Urgent Saved: {$count['saved-urgent']}</li>
            </ul>";
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
