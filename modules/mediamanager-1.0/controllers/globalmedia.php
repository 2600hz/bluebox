<?php
defined('SYSPATH') or die('No direct access allowed.');
/*
* Bluebox Modular Telephony Software Library / Application
*
* The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
* you may not use this file except in compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/.
*
* Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
* express or implied. See the License for the specific language governing rights and limitations under the License.
*
* The Original Code is Bluebox Telephony Configuration API and GUI Framework.
* The Original Developer is the Initial Developer.
* The Initial Developer of the Original Code is Darren Schreiber
* All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
*
* Contributor(s):
*
*
*/
/**
 * mediamanager.php - Media Management System
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package MediaManager
 */
class GlobalMedia_Controller extends Bluebox_Controller
{
    protected $baseModel = 'MediaFile';

    protected $knownTypes = array('mp3', 'wav', 'ogg');

    protected $soundPath = "/usr/local/freeswitch/sounds/";

    public function __construct()
    {
        parent::__construct();
        $this->uploadPath = Kohana::config('upload.directory') . "/" . $this->session->get('user_id') . "/";
    }

    public function index()
    {
        $this->template->content = new View('globalmedia/index');
        $this->view->filetree = filetree::php_file_tree($this->soundPath, "javascript:filterPath('[link]');", FALSE, '/^8000$|^16000$|^32000$|^48000$/');
        javascript::add('php_file_tree_jquery.js');
        stylesheet::add('php_file_tree.css');

        // Remember the last path that was clicked on. We use this for uploads and other items.
        // Why store this as a session var? Prevents people from screwing with paths they aren't supposed to be
        // able to touch. In this way, we NEVER expose paths directly to our scripts with data from the outside
        if (isset($_REQUEST['searchString'])) {
            $_SESSION['globalmedia']['path'] = $_REQUEST['searchString'];
        } else {
            unset ($_SESSION['globalmedia']['path']);
        }

        // Build a grid with a hidden device_id, device_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => '&nbsp;',
            'gridName' => 'MediaGrid'
        ))->add('mediafile_id', 'ID', array(
            'width' => '80',
            'hidden' => true,
            'key' => true
        ))->add('file', 'Filename', array(
            'width' => '40',
            'search' => true,
            'callback' => array('MediaFile', 'getBaseName')
        ))->add('path', 'Path', array(
            'width' => '80',
            'hidden' => true,
            'key' => true,
            'search' => true,
        ))->add('description', 'Description', array(
            'width' => '80',
            'search' => true
        ))->add('size', 'File Size', array(
            'width' => '40',
            'align' => 'right',
            'callback' => array(
                'function' => array('MediaFile', 'getSize'),
                'arguments' => 'registry'
            )
        ))->add('length', 'Length', array(
            'width' => '40',
            'align' => 'right',
            'callback' => array(
                'function' => array('MediaFile', 'getLength'),
                'arguments' => 'registry'
            )
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('globalmedia/details', 'Details', array(
            'arguments' => 'mediafile_id',
            'attributes' => array('class' => 'qtipAjaxForm')
        ))->addAction('globalmedia/download', 'Download', array(
            'arguments' => 'mediafile_id'
        ))->addAction('globalmedia/delete', 'Delete', array(
            'width' => '60',
            'arguments' => 'mediafile_id'
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON
        $this->view->grid = $this->grid->produce();
    }

    public function scan() {
        // TODO: Make this run
        
        MediaScanner::scan($this->soundPath, $this->knownTypes);
    }

    public function details($mediaId) {
        $this->view->mediaId = $mediaId;
        
        $this->view->media = Doctrine::getTable('MediaFile')->find($mediaId, Doctrine::HYDRATE_ARRAY);
    }

    private function locateFile($media, $sampleRate = NULL) {
        $file = $this->soundPath . $media['file'];

        // See if the file exists. If not, try adding sample rates to the path
        if (!file_exists($file)) {
            $found = FALSE;
            $file = $this->soundPath . dirname($media['file']) . '/' . $sampleRate . '/' . basename($media['file']);

            if (($sampleRate) and (file_exists($file))) {
                $found = TRUE;
            } else {
                if (isset($media['registry']['rates'])) foreach ((array)$media['registry']['rates'] as $rate) {
                    $file = $this->soundPath . dirname($media['file']) . '/' . $rate . '/' . basename($media['file']);
                    if (file_exists($file)) {
                        $found = TRUE;
                        continue;
                    }
                }
            }

            if (!$found)
                return FALSE;
        }
        
        return $file;
    }

    public function visualize($mediaId) {
        $media = Doctrine::getTable('MediaFile')->find($mediaId, Doctrine::HYDRATE_ARRAY);
        $file = $this->locateFile($media);
        if (!$file) {
            die();
        }

        // Initialize audio analysis routine
        $audioFile = new AudioFile();
        $audioFile->loadFile($file);

        $audioFile->visual_height = 250;
        $audioFile->visual_background_color = '#FFFFFF';
        $audioFile->visual_border_color = '#FFFFFF';
        $audioFile->visual_grid_color = '#CCCCCC';
        $audioFile->visual_graph_color = '#0000FF';

        header('Content-type: image/jpeg');
        $audioFile->getVisualization(NULL);
        
        flush();
        exit();
    }


    public function download($mediaId, $sampleRate = NULL, $stream = FALSE)
    {
        $file = Doctrine::getTable('MediaFile')->find($mediaId, Doctrine::HYDRATE_ARRAY);
        $fullPath = $this->locateFile($file, $sampleRate);
        
        $name = basename($file['file']);
        if ($file['registry']['type'] == 'MPEG') {
            $mime = 'audio/mpeg';
        } else {
            $mime = 'audio/x-wav';
        }
        header(sprintf('Content-type: %s', $mime));
        if (!$stream) {
            // Include filename and attachment disposition only if we don't want to stream
            header(sprintf('Content-Disposition: attachment; filename="%s"', $name));
        }
        readfile($fullPath);
        die();
    }















    public function add()
    {
        javascript::add('ajaxupload');
        
        $this->view->title = 'Upload Media';

        $maxFilesize = $this->tobytes(ini_get('upload_max_filesize'));
        $maxPost = $this->tobytes(ini_get('post_max_size'));
        if ($maxFilesize <= $maxPost) {
            $this->view->maxUpload =  __('Max file size that can uploaded is limited by upload_max_filesize to ') . $this->bytesToMb($maxFilesize);
        } else {
            $this->view->maxUpload =  __('Max file size that can uploaded is limited by post_max_size to ') . $this->bytesToMb($maxPost) .'.  ';
            $this->view->maxUpload .= __('If you attempt to upload something larger than this the page will simply reload.');
        }
        
        if (isset($_FILES['upload'])) {
            Kohana::log('debug', 'File uploaded. ' . print_r($_FILES, TRUE));
            switch ($_FILES['upload']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    message::set('The uploaded file exceeds the upload_max_filesize directive in php.ini');
                    break;

                case UPLOAD_ERR_FORM_SIZE:
                    message::set('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
                    break;

                case UPLOAD_ERR_PARTIAL:
                    message::set('The uploaded file was only partially uploaded');
                    break;

                case UPLOAD_ERR_NO_FILE:
                    message::set('No file was uploaded');
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    message::set('Missing a temporary folder');
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    message::set('Failed to write file to disk');
                    break;

                case UPLOAD_ERR_EXTENSION:
                    message::set('File upload stopped by extension');
                    break;

              case UPLOAD_ERR_OK:
                    $description = (isset($_POST['upload']['description']) ? $_POST['upload']['description'] : '');
                    $uploadfile = $this->soundPath . $_SESSION['globalmedia']['path'] . '/' . basename($_FILES['upload']['name']);
                  
                    if ($this->upload($_FILES['upload']['tmp_name'], $uploadfile, $this->soundPath, $description, TRUE)) {
                        message::set('Uploaded file', 'success');
                    }
                    break;

                default:
                    message::set('Unknown error');
            }
        }
    }

/*    public function edit($id)
    {
        $this->view->title = 'Edit Media';
        $file = Doctrine::getTable('File')->find($id);
        if (!$file) {
            // Send any errors back to the index
            message::set('Unable to locate file id ' . $id, array(
                'redirect' => Router::$controller
            ));
            return TRUE;
        }
        if ($this->submitted() && isset($_POST['mediamanager']['description'])) {
            $file->description = $_POST['mediamanager']['description'];
            if ($file->save()) {
                message::set('File updated', array(
                    'type' => 'success',
                    'redirect' => Router::$controller
                ));
            }
        }
        $this->view->mediamanager = array('description' => $file->description);
    }

    public function preview($id)
    {
        stylesheet::add('mediamanager', 40);
        $this->view->title = 'Preview Media';
        $this->view->url = url::site('mediamanager/listen/' . $id);
    }
 
    public function listen($id)
    {
        $file = Doctrine::getTable('File')->find($id);
        $fullPath = $file->path . $file->name;
        $name = $file->name;
        $mime = $file->type;
        header(sprintf('Content-type: %s', $mime));
        readfile($fullPath);
        die();
    }

    public function delete($id)
    {
        $this->stdDelete($id);
    }*/

    private function upload($tmpfile, $destfile, $basePath, $description = '', $replace = false)
    {
        $dir = dirname($destfile);
        $shortname = str_replace($basePath, '', MediaScanner::NormalizeFSNames($destfile));

        /* can we write to the target folder? */
        if (!filesystem::is_writable($dir)) {
            message::set('The path ' . $dir . ' is not writable!');
            return FALSE;
        }

        $audioFile = new AudioFile();
        $audioFile->loadFile($tmpfile);

        // Create folder where this file will go and move file there
        $destfile = dirname($destfile) . '/' . $audioFile->wave_framerate . '/' . basename($destfile);
        $this->createFolder(dirname($destfile));

        if (!is_writable(dirname($destfile)) or (file_exists($destfile) and !is_writable($destfile))) {
            message::set(dirname($destfile) . ' is not writable');
            return FALSE;
        }

        try {
            move_uploaded_file($tmpfile, $destfile);
        } catch (Exception $e) {
            message::set('Unable to move uploaded file into ' . $destfile . '. ' . $e->getMessage());
            return FALSE;
        }

        // See if this is in the DB
        $mediaFile = Doctrine::getTable('MediaFile')->findOneByFile($shortname);
        if ($mediaFile) {
            // Note that this is a bit dangerous and could use improvement.
            // We assume that all other properties in the file we just found match the file already uploaded.
            // That means if someone uploads the wrong audio file, it kinda messes things up big time.
            if (!in_array($audioFile->wave_framerate, (array)$mediaFile['registry']['rates'])) {
                Kohana::log('debug', 'Updating ' . $shortname . " with sample rate " . $audioFile->wave_framerate . "... ");
                $mediaFile['registry'] = array_merge_recursive($mediaFile['registry'], array('rates' => $audioFile->wave_framerate));;
                $mediaFile->save();
            } else {
                Kohana::log('debug', 'SKIPPED DB UPDATE - Nothing to update on ' . $shortname . " with sample rate " . $audioFile->wave_framerate . "... ");
            }

        } else {
            // NEW FILE! Do lots of stuff

            // Save info about file
            $mediaFile = new MediaFile();
            $mediaFile['file'] = $shortname;
            $mediaFile['path'] = dirname($mediaFile['file']);   // We track the path separately to ease searching
            $mediaFile['account_id'] = 1;

            // See if we know this filename, description & category from the XML info
            if (isset($descriptions[$shortname])) {
                $mediaFile['description'] = $descriptions[$shortname];
            } else {
                $mediaFile['description'] = 'Unknown';
            }

            Kohana::log('debug', 'Adding ' . $mediaFile['file'] . " to the database.");

            $audioInfo = array( 'type' => $audioFile->wave_type,
                                'compression' => $audioFile->wave_compression,
                                'channels' => $audioFile->wave_channels,
                                'rates' => $audioFile->wave_framerate,
                                'byterate' => $audioFile->wave_byterate,
                                'bits' => $audioFile->wave_bits,
                                'size' => $audioFile->wave_size,
                                'length' => $audioFile->wave_length);

            $mediaFile['registry'] += $audioInfo;

            $mediaFile->save();

        }
    }

    public function createFolder($path) {
        /* check if folder exists */
        if (!is_dir($this->uploadPath)) {
            if (!filesystem::createDirectory($this->uploadPath)) {
                message::set('The path ' . $this->uploadPath . 'does not exist and could not be created!');
                return false;
            }
        }
    }

    private function fileExists($filename)
    {
        $q = Doctrine_Query::create()->select('f.file_id')->from('File f')->where('f.name = ? AND f.user_id = ? AND f.path = ?', array(
            $filename,
            $_SESSION['user_id'],
            $this->uploadPath
        ));
        if ($q->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function validate()
    {
        $files = Validation::factory($_FILES)->add_rules('upload', 'upload::valid', 'upload::required', 'upload::size[' . ini_get('upload_max_filesize') . ']');
        return $files->validate();
    }

    private function tobytes($val)
    {
        $val = trim($val);
        $last = strtolower(substr($val, strlen($val / 1) , 1));
        if ($last == 'g') $val = $val * 1024 * 1024 * 1024;
        if ($last == 'm') $val = $val * 1024 * 1024;
        if ($last == 'k') $val = $val * 1024;
        return $val;
    }
    
    private function bytesToMb($byte)
    {
        return $byte / 1048576 . 'Mb';
    }

    public function qtipAjaxReturn($data) {
        javascript::codeBlock('$(\'.jqgrid_instance\').trigger("reloadGrid");');
    }
}
