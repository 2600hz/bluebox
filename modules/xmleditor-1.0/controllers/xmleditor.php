<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * XML File Editor Support
 *
 * @author Darren Schreiber
 */
class XmlEditor_Controller extends Bluebox_Controller 
{
    public function index() 
    {
        $this->view->filetree = filetree::php_file_tree(Kohana::config('freeswitch.cfg_root'), "javascript:FileManager.load('[link]');", array('xml', 'conf', 'tpl'));

        javascript::add('php_file_tree_jquery.js');

        stylesheet::add('php_file_tree.css');

        javascript::add('editarea/edit_area_full.js');
    }

    public function load() 
    {
        $this->auto_render = false;

        $base = Kohana::config('freeswitch.cfg_root');

        $filename = str_replace($base, '', $_GET['filename']);

        $filename = escapeshellcmd(str_replace('..', '', $filename));

        echo file_get_contents($base . $filename);
    }

    public function save() 
    {
        $this->auto_render = false;

        $base = Kohana::config('freeswitch.cfg_root');

        $filename = str_replace($base, '', $_POST['filename']);

        $filename = escapeshellcmd(str_replace('..', '', $filename));

        if (is_writable($base . $filename)) 
        {
            try 
            {
                $dangerousData = Input::instance()->get_unfiltered('_POST');

                $fp = fopen($base . $filename, "w");

                fwrite($fp, $dangerousData['data']);

                fclose($fp);

                echo 'Save of ' . $base . $filename . ' was successful.';
            } 
            catch (Exception $e) 
            {
                echo 'Save failed. ' . $e->getMessage();
            }
        } 
        else 
        {
            echo 'File is not writable.';
        }
    }
}
