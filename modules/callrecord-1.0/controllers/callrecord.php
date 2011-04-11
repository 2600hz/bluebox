<?php defined('SYSPATH') or die('No direct access allowed.');

class CallRecord_Controller extends Bluebox_Controller {

    public function listen( $uuid) {
        $this->auto_render = FALSE;

        $file = CallRecord::getFile($uuid);
        if(!file_exists($file)) {
            Kohana::log('error', 'Can\'t access file: '  . $file);
            return;
        }
        $this->listenFile($file);
    }

    public function listenFile($filename)
    {
        header("Content-type: audio/wav");
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        die();
    }

    public function get($uuid) {
        $this->auto_render = FALSE;

        $file = CallRecord::getFile($uuid);
        if(!file_exists($file)) {
            Kohana::log('error', 'Can\'t access file: '  . $file);
            return;
        }
        $this->getFile($file);
    }

    public function getFile($filename)
    {
        header('Content-type: audio/wav');
        header('Content-Length: ' . filesize($filename));
        header('Content-disposition: attachment; filename="' . basename($filename) . '"');
        readfile($filename);
        die();
    }
}
