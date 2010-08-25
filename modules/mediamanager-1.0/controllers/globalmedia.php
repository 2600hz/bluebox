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

    public function __construct()
    {
        parent::__construct();
        $this->uploadPath = Kohana::config('upload.directory') . "/" . $this->session->get('user_id') . "/";
    }

    public function index()
    {
        $this->template->content = new View('globalmedia/index');
        javascript::add('php_file_tree_jquery.js');
        stylesheet::add('php_file_tree.css');

        // Collect a list of paths in the system, to be displayed as a list
        $this->view->filetree = filetree::php_file_tree(Media::getAudioPath(), "javascript:filterPath('[link]');", FALSE, '/^8000$|^16000$|^32000$|^48000$/');

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

    public function delete($mediafile_id) {
      $base = strtolower($this->baseModel);
      $this->createView();

      $this->loadBaseModel($mediafile_id);

      if ( $action = $this->submitted(array('submitString' => 'delete')) ) {
	Event::run('bluebox.deleteOnSubmit', $action);

	if ( ($action == self::SUBMIT_CONFIRM) ) {
	  if ( ($mf = Doctrine::getTable('MediaFile')->findOneByMediafileId($mediafile_id, Doctrine::HYDRATE_ARRAY)) === NULL ) {
	    messge::set('Unable to delete media file.');
	    $this->exitQtipAjaxForm();
	    url::redirect(Router_Core::$controller);
	  } else {
	    if ( empty($mf['registy']) || empty($mf['registry']['rate']) ) {
	      $mf['registry']['rate'] = array();
	      @unlink($mf['file']);
	    }

	    foreach ( $mf['registry']['rates'] as $rate ) {
	      $f = Media::getAudioPath() . $mf['path'] . DIRECTORY_SEPARATOR . $rate . DIRECTORY_SEPARATOR;
	      @unlink($f . basename($mf['file']));
	    }

	    Doctrine_Query::create()
	      ->delete('MediaFile')
	      ->where('mediafile_id = ?', $mf['mediafile_id'])
	      ->execute();

	    message::set('Removed ' . basename($mf['file']));

	    $this->returnQtipAjaxForm(NULL);

	    url::redirect(Router_Core::$controller);
	  }
	} else if ($action == self::SUBMIT_DENY) {
	  $this->exitQtipAjaxForm();

	  url::redirect(Router_Core::$controller);
	}
      }

      $this->prepareDeleteView(NULL, $mediafile_id);
    }

    public function scan() {
      // TODO: Make this run in the background
      MediaScanner::scan(Media::getAudioPath(), $this->knownTypes);
      $this->returnQtipAjaxForm(NULL);
    }

    public function details($mediaId) {
        $this->view->mediaId = $mediaId;
        $this->view->media = Doctrine::getTable('MediaFile')->find($mediaId, Doctrine::HYDRATE_ARRAY);
    }

    private function locateFile($media, $sampleRate = NULL) {
        $file = Media::getAudioPath() . $media['file'];

        // See if the file exists. If not, try adding sample rates to the path
        if (!file_exists($file)) {
            $found = FALSE;
            $file = Media::getAudioPath() . dirname($media['file']) . '/' . $sampleRate . '/' . basename($media['file']);

            if (($sampleRate) and (file_exists($file))) {
                $found = TRUE;
            } else {
                if (isset($media['registry']['rates'])) foreach ((array)$media['registry']['rates'] as $rate) {
                    $file = Media::getAudioPath() . dirname($media['file']) . '/' . $rate . '/' . basename($media['file']);
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


    public function download($mediaId, $sampleRate = NULL, $stream = FALSE) {
      $file = Doctrine::getTable('MediaFile')->find($mediaId, Doctrine::HYDRATE_ARRAY);
      $fullPath = $this->locateFile($file, $sampleRate);

      $name = basename($file['file']);
      if ( in_array($file['registry']['type'], array('MPEG', 'mp3')) ) {
	$mime = 'audio/mpeg';
      } else if ( ! strcmp($file['registry']['type'], 'ogg') ) {
	$mime = 'audio/ogg';
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

    public function add() {
      //javascript::add('ajaxupload');

      $this->view->title = 'Upload Media';

      $maxFilesize = ini_get('upload_max_filesize');
      $maxPost = ini_get('post_max_size');
      if ($maxFilesize <= $maxPost) {
	$this->view->maxUpload =  __('Max file size that can uploaded is limited by upload_max_filesize to ') . $maxFilesize;
      } else {
	$this->view->maxUpload =  __('Max file size that can uploaded is limited by post_max_size to ') . $maxPost .'.  ';
	$this->view->maxUpload .= __('If you attempt to upload something larger than this the page will simply reload.');
      }

      Kohana::log('debug', print_r($_POST, TRUE) . print_r($_FILES, TRUE));

      if (isset($_FILES['upload'])) {
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
	  $uploadfile = Media::getAudioPath() . $_POST['upload']['path'] . '/' . basename($_FILES['upload']['name']);

	  if ($this->upload($_FILES['upload']['tmp_name'], $uploadfile, Media::getAudioPath(), $description, TRUE)) {
	    message::set('Uploaded file', 'success');
	  }
	  break;

	default:
	  message::set('Unknown error');
	}
      }

      $this->view->soundPath = Media::getAudioPath();
    }

    private function upload($tmpfile, $destfile, $basePath, $description = '', $replace = false) {
      $dir = dirname($destfile);
      $shortname = str_replace($basePath, '', MediaScanner::NormalizeFSNames($destfile));

      /* can we write to the target folder? */
      if (!filesystem::is_writable($dir)) {
	message::set('The path ' . $dir . ' is not writable!');
	return FALSE;
      }

      $audioInfo = MediaScanner::getAudioInfo($tmpfile);

      // Create folder where this file will go and move file there
      if ( ! empty($audioInfo) ) {
	$destfile = dirname($destfile) . '/' . $audioInfo['rates'][0] . '/' . basename($destfile);
      } else {
	$destfile = dirname($destfile) . '/' . basename($destfile);
      }

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
	if (!in_array($audioInfo['byterate'], (array) $mediaFile['registry']['rates'])) {

	  Kohana::log('debug', 'Updating ' . $shortname . "...");
	  $mediaFile['registry'] = array_merge_recursive($mediaFile['registry'], $audioInfo);
	  $mediaFile['description'] = strlen($description) > 0 ? $description : $mediaFile['description'];
	  $mediaFile->save();
	} else {
	  if ( strcmp($mediaFile['description'], $description) ) {
	    $mediaFile['description'] = $description;
	    $mediaFile->save();
	  } else {
	    Kohana::log('debug', 'SKIPPED DB UPDATE - Nothing to update on ' . $shortname . " with sample rate " . $audioInfo['byterate'] . "... ");
	  }
	}
	message::set('Successfully updated audio file in the system.');

	url::redirect(Router_Core::$controller . '/index');
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
	} else if ( strlen($description) > 0 ) {
	  $mediaFile['description'] = $description;
	} else {
	  $mediaFile['description'] = 'Unknown';
	}

	Kohana::log('debug', 'Adding ' . $mediaFile['file'] . " to the database.");

	$mediaFile['registry'] += $audioInfo;

	$mediaFile->save();

	message::set('Successfully added audio file to the system.');

	url::redirect(Router_Core::$controller . '/index');
      }
    }

    public function create() {
      $reqPath = '';

      if (isset($_POST['path']) and isset($_POST['newfolder'])) {
	$reqPath = $_POST['path'];
	$path = Media::getAudioPath() . $reqPath;

	kohana::log('debug', 'Creating ' . $path . DIRECTORY_SEPARATOR . $_POST['newfolder']);
	if ($this->createFolder($path . DIRECTORY_SEPARATOR . $_POST['newfolder'])) {
	  message::set('Folder created.');

	  url::redirect(Router_Core::$controller . '/index');
	} else {
	  message::set('The path ' . $_POST['path'] . ' does not exist or could not be created! Does your web user have write access?');
	}
      }

      plugins::views($this);

      $this->view->soundPath = Media::getAudioPath();
      $this->view->reqPath = $reqPath;
    }

    /**
     * Helper function to create a directory if it doesn't exist.
     * @param <type> $path Path to dir to create
     * @return <type>
     */
    private function createFolder($path) {
        /* check if folder exists */
        return (is_dir($path) or filesystem::createDirectory($path));
    }

    /*public function buildDescriptionsFromFs()
    {
        $descriptions = MediaScanner::scanXml();
        var_dump($descriptions);
        $fp = fopen(MODPATH . '/mediamanager-1.0/audio_descriptions.ini', 'w');
        foreach ($descriptions as $key => $value) {
            fputs($fp, '"' . $key . '","' . $value . "\"\n");
        }
        fclose($fp);
        exit();
    }*/

}
