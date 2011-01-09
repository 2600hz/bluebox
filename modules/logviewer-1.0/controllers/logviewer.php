<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class LogViewer_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'LogViewer';

    public function index() {
        stylesheet::add('logviewer', 50);

    }

    public function stream() {
        // Turn off auto_render - other json_encode breaks!
        $this->auto_render = FALSE;

        //Not needed
        //session_start();

        // Initialize Session
        if(!isset($_SESSION['logviewer_pos'])) {
            $logfile = '/usr/local/freeswitch/log/freeswitch.log';

            $_SESSION['logviewer_pos'] = filesize($logfile) - 1200;
            if($_SESSION['logviewer_pos'] < 0) {
                $_SESSION['logviewer_pos'] = 0;
            }

            $_SESSION['logviewer_file'] = $logfile;
        }
        // END Initialize
        
        // Start timing
        $last_modify_time = 0;
        if(isset($_GET['timestamp']))
            $last_modify_time = $_GET['timestamp'];

        $current_modify_time = filemtime($_SESSION['logviewer_file']);

        /* This is the blocking version */
        //while ($current_modify_time <= $last_modify_time) {
        //    usleep(10000);
        //    clearstatcache();
        //    $current_modify_time = filemtime($_SESSION['logviewer_file']);
        //}

        /* This is the non-blocking version */
        usleep(10000);
        clearstatcache();
        
        if($current_modify_time <= $last_modify_time) {
            return null;
        }
        /* End non-blocking */

        $stream_info = array();
        $stream_info["timestamp"] = $current_modify_time;
        
        // Start read
        $json_reply = array();

        $log_pointer = fopen($_SESSION['logviewer_file'], 'r');
        fseek($log_pointer, $_SESSION['logviewer_pos']);

        //Skip to the first complete line....
        while(($char = fgetc($log_pointer)))
            if ($char == '\n') break;

        while(!feof($log_pointer)) {
            $line = fgets($log_pointer);
            if(trim($line) != '')
                $json_reply[] = array("type" => "log", "data" => $line);
        }
        
        $_SESSION['logviewer_pos'] = ftell($log_pointer);

        fclose($log_pointer);
        // END Read

        // Setup the stream
        $stream = array();
        $stream["json_reply"] = $json_reply;
        $stream["stream_info"] = $stream_info;
        // END stream

        echo json_encode($stream);
    }

}