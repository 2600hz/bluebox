<?php
/**
 * XML File Editor Support
 *
 * @author Darren Schreiber
 */
class XmlEditor_Controller extends FreePbx_Controller {
    public function __construct() {
        // Turn off XSS checks because they interfere with XML submits.
        if (Router::$method == 'save') {
            Kohana::config_set('core.global_xss_filtering', FALSE);
        }
        parent::__construct();
    }

    public function index() {
        $this->view->filetree = filetree::php_file_tree(Kohana::config('freeswitch.cfg_root'), "javascript:FileManager.load('[link]');", array('xml', 'conf', 'tpl'));
        javascript::add('php_file_tree_jquery.js');
        stylesheet::add('php_file_tree.css');
        javascript::add('editarea/edit_area_full.js');
    }

    public function load() {
        $this->auto_render = false;

        $base = Kohana::config('freeswitch.cfg_root');
        $filename = str_replace($base, '', $_GET['filename']);
        $filename = escapeshellcmd(str_replace('..', '', $filename));

        echo file_get_contents($base . $filename);
    }

    public function save() {
        $this->auto_render = false;

        $base = Kohana::config('freeswitch.cfg_root');
        $filename = str_replace($base, '', $_POST['filename']);
        $filename = escapeshellcmd(str_replace('..', '', $filename));

        if (is_writable($base . $filename)) {
            try {
                $fp = fopen($base . $filename, "w");
                fwrite($fp, $_POST['data']);
                fclose($fp);
                echo 'Save of ' . $base . $filename . ' was successful.';
            } catch (Exception $e) {
                echo 'Save failed. ' . $e->getMessage();
            }
        } else {
            echo 'File is not writable.';
        }
    }
}
