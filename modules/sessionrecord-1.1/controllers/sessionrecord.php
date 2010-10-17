<?php defined('SYSPATH') or die('No direct access allowed.');

class SessionRecord_Controller extends Bluebox_Controller {

    public function listen( $uuid) {
        $this->auto_render = FALSE;

        $file = SessionRecord::getFile($uuid);
        if(!file_exists($file)) {
            Kohana::log('error', 'Can\'t access file: '  . $file);
            return;
        }

        header("Content-type: audio/wav");
        header('Content-Length: ' . filesize($file));
        readfile($file);
        die();
    }


}
