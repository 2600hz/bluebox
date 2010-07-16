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

    public function getDetails()
    {
        return 'Details';
    }

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
        //MediaScanner::scan();
    }

    public function details($mediaid) {
        
    }

    public function add()
    {
        javascript::add('jquery_uploadify');
        javascript::add('swfobject');
        stylesheet::add('uploadify');
        
        $this->view->title = 'Upload Media';

        $maxFilesize = $this->tobytes(ini_get('upload_max_filesize'));
        $maxPost = $this->tobytes(ini_get('post_max_size'));
        if ($maxFilesize <= $maxPost) {
            $this->view->maxUpload =  __('Max file size that can uploaded is limited by upload_max_filesize to ') . $this->bytesToMb($maxFilesize);
        } else {
            $this->view->maxUpload =  __('Max file size that can uploaded is limited by post_max_size to ') . $this->bytesToMb($maxPost) .'.  ';
            $this->view->maxUpload .= __('If you attempt to upload something larger than this the page will simply reload.');
        }
        
        if ($this->submitted()) {
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
                    switch ($this->input->post('type')) {
                        case 'package':
                            $description = '';
                            $replace = TRUE;
                            break;

                        default:
                            if (empty($_POST['mediamanager']['description'])) {
                                $description = '';
                            } else {
                                $description = $_POST['mediamanager']['description'];
                            }
                            $replace = !empty($_POST['mediamanager']['replace']);
                            break;
                    }
                    if ($this->upload($replace, $description)) {
                        message::set('Uploaded file', 'success');







                    }
                    break;

                default:
                    message::set('Unknown error');
            }
        }
    }

    public function uploadify()
    {
        header('Content-type: application/x-shockwave-flash');
        readfile(MODPATH . 'mediamanager-1.0/assets/uploadify.swf');
        die();
    }

    public function upload($session = NULL)
    {
        if ($session) {
            session_id($session);
        }

        //folder where the files are stored at
        //you may need to chmod this folder to 775 or 777 for it to work
        $src_folder = '/tmp/';

        //if javascript is disabled, the ftp will still work
        if (isset($_FILES["file"])) {
            if ($_FILES["file"]["error"] > 0) {
              $error = 'Error Uploading!';
            } else {
            $count = '1';
            $file_loc = $path . $_FILES["file"]["name"];
            $base = $_FILES["file"]["name"];
            while ( file_exists($file_loc) ) {
                $file_loc = $path . $count.'-'. $_FILES["file"]["name"];
                $base = $count.'-'. $_FILES["file"]["name"];
                $count++;
            }
            move_uploaded_file($_FILES["file"]["tmp_name"], $file_loc);
            }
        }
    }

    public function edit($id)
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

    public function download($id)
    {
        $file = Doctrine::getTable('File')->find($id);
        $fullPath = $file->path . $file->name;
        $name = $file->name;
        $mime = $file->type;
        header(sprintf('Content-type: %s', $mime));
        header(sprintf('Content-Disposition: attachment; filename="%s"', $name));
        readfile($fullPath);
        die();
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
        //header(sprintf('Content-Disposition: attachment; filename="%s"', $name));
        readfile($fullPath);
        die();
    }

    public function delete($id)
    {
        $this->stdDelete($id);
    }

    private function old_upload($replace = false, $description = '')
    {
        /* check if folder exists */
        if (!is_dir($this->uploadPath)) {
            if (!filesystem::createDirectory($this->uploadPath)) {
                message::set('The path ' . $this->uploadPath . 'does not exist and could not be created!');
                return false;
            }
        }
        /* can we write to the target folder? */
        if (!filesystem::is_writable($this->uploadPath)) {
            message::set('The path ' . $this->uploadPath . ' is not writable!');
            return false;
        }
        if ($this->validate()) {
            $audio_bit_rate = 0;
            $audio_sample_rate = 0;
            $audio_codec = 'none';
            $duration = 0.00;
            if (extension_loaded('ffmpeg')) {
                $media = new ffmpeg_movie($_FILES['upload']['tmp_name']);
                $duration = $media->hasAudio() ? sprintf("%01.2f", $media->getDuration()) : $duration;
                $audio_bit_rate = $media->hasAudio() ? $media->getAudioBitRate() : $audio_sample_rate;
                $audio_sample_rate = $media->hasAudio() ? $media->getAudioSampleRate() : $audio_sample_rate;
                $audio_codec = $media->hasAudio() ? $media->getAudioCodec() : $audio_codec;
            }
            if ($this->fileExists($_FILES['upload']['name'])) {
                if ($replace) {
                    $file->user_id = $_SESSION['user_id'];
                    $file = Doctrine::getTable('File')->findOneByName($_FILES['upload']['name']); // so much better in doctrine 1.2!!!!
                    $file->size = $_FILES['upload']['size'];
                    $file->description = $description;
                    $file->path = $this->uploadPath;
                    $file->duration = $duration;
                    $file->audio_bit_rate = $audio_bit_rate;
                    $file->audio_sample_rate = $audio_sample_rate;
                    try {
                        $file->save();
                    } catch(Exception $e) {
                        message::set('Failed to update file tracker!');
                        return false;
                    }
                } else {
                    message::set('The file already exists, try checking the "Replace File" checkbox');
                    return false;
                }
            } else {
                $file = new File();
                $file->user_id = $_SESSION['user_id'];
                $file->name = $_FILES['upload']['name'];
                $file->size = $_FILES['upload']['size'];
                $file->type = $_FILES['upload']['type'];
                $file->description = $description;
                $file->path = $this->uploadPath;
                $file->duration = $duration;
                $file->audio_bit_rate = $audio_bit_rate;
                $file->audio_sample_rate = $audio_sample_rate;
                try {
                    $file->save();
                } catch(Exception $e) {
                    message::set('Failed to add to file tracker!');
                    return false;
                }
            }
            $filename = upload::save('upload', $_FILES['upload']['name'], $this->uploadPath); //moves tmp file
            
            return true;
        } else {
            Kohana::log('info', 'Failed Kohana validation rules during upload');
            message::set('Failed to upload file');
            return false;
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
