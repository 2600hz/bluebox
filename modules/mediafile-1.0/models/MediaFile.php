<?php defined('SYSPATH') or die('No direct access allowed.');

class MediaFile extends Bluebox_Record
{
    private $uploaded_file = NULL;
    
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('mediafile_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 255, array('notblank' => true));
        $this->hasColumn('description', 'string');
        $this->hasColumn('file', 'string', 255, array('notblank' => true));
        $this->hasColumn('path', 'string', 512);
        $this->hasColumn('type', 'string', 512);
        $this->hasColumn('compression', 'float', 11);
        $this->hasColumn('channels', 'integer', 3, array('unsigned' => true));
        $this->hasColumn('rates', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('bits', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('size', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('length', 'float', 11);
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {     
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }

    public function postSave()
    {
        if (!$this->uploaded_file)
        {
            return;
        }

        kohana::log('debug', 'Moving upload "' .$this->uploaded_file['tmp_name'] .'" to "' .$this->filepath(TRUE) .'"');

        if (!upload::save($this->uploaded_file, $this->get('file'), $this->filepath()))
        {
            throw new Exception('Unable to save file to system');
        }
    }

    public function postDelete()
    {
        $filepath = $this->filepath(TRUE);

        kohana::log('debug', 'Deleting file ' .$filepath);

        if (is_file($filepath) AND !unlink($filepath))
        {
            throw new Exception('Unable to delete file from system, please check permissions of "' .$filepath .'"');
        }
    }

    public function get_resampled($rates = NULL)
    {
        $resampled = Doctrine_Query::create()
             ->from('MediaFile')
             ->where('file = ?', $this->get('file'))
             ->orderBy('rates');

        if (!empty($rate))
        {
            $resampled->andWhereIn('rates', (array)$rates);
        }

        Event::run('mediafile.get_resampled', $resampled);

        return $resampled->execute();
    }

    public function prepare_upload($uploadvar = 'upload')
    {
        if (!arr::get($_FILES, 'mediafile', 'name', $uploadvar))
        {
            kohana::log('error', 'Attempted to prepare upload without file');

            return 'Please provide a file to upload';
        }

        $uploadedFile = arr::get(arr::rotate($_FILES['mediafile']), $uploadvar);

        switch ($uploadedFile['error'])
        {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize';

            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE';

            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';

            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';

            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';

            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';

            case UPLOAD_ERR_EXTENSION:
                return 'Invalid file extension (type)';

            case UPLOAD_ERR_OK:

                if (!$this->get('file'))
                {
                    $this->set('file', $uploadedFile['name']);
                }

                if (!$this->get('name'))
                {
                    $this->set('name', pathinfo($uploadedFile['name'], PATHINFO_FILENAME));
                }

                if ($this->get('path'))
                {
                    $path = trim($this->get('path'), DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR;

                    $this->set('path', $path);
                }

                if (!is_file($uploadedFile['tmp_name']))
                {
                    kohana::log('error', 'Unable to locate file in temporary storage ' .$uploadedFile['tmp_name']);

                    return 'Unable to upload file';
                }

                if (!($mediainfo = MediaLib::getAudioInfo($uploadedFile['tmp_name'])))
                {
                    kohana::log('error', 'Unable to determine audio info for tmp upload file "' .$uploadedFile['tmp_name'] .'"');

                    return 'Upload is not a valid audio file or format';
                }

                $this->fromArray($mediainfo);

                if (kohana::config('mediafile.upload_to_rate_folders'))
                {
                    $rate = $this->get('rates');

                    $path = $this->get('path');

                    if (in_array($rate, kohana::config('mediafile.default_rates')) AND
                            !strstr($path, $rate .DIRECTORY_SEPARATOR))
                    {
                        $path .= $this->get('rates') .DIRECTORY_SEPARATOR;

                        $this->set('path', $path);
                    }
                    else if (($unknownPath = kohana::config('mediafile.unknown_rate_folder')))
                    {
                        $path .= trim($unknownPath, DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR;

                        $this->set('path', $path);
                    }
                }

                $directory = $this->filepath();

                if (!$directory OR (!filesystem::is_writable($directory) AND !filesystem::createDirectory($directory)))
                {
                    kohana::log('error', 'The configured media dir is not writable, please chmod "' .$directory .'"');

                    return 'Media collection directory is not writable';
                }

                $this->uploaded_file = $uploadedFile;
                
                break;
                
            default:
                return 'Upload failed for an unspecified reason';
        }

        return FALSE;
    }

    public function filepath($full = FALSE, $actual = TRUE)
    {
        $basepath = kohana::config('upload.directory');

        Event::run('mediafile.basepath', $basepath);

        $filepath = rtrim($basepath, DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR;

        if ($this->get('path'))
        {
            $filepath .= rtrim($this->get('path'), DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR;
        }

        if (!$actual AND kohana::config('mediafile.hide_rate_folders'))
        {
            $default_rates = kohana::config('mediafile.default_rates');

            $default_rates[] = trim(kohana::config('mediafile.unknown_rate_folder'), DIRECTORY_SEPARATOR);
            
            foreach ($default_rates as &$value)
            {
                $value = DIRECTORY_SEPARATOR .$value .DIRECTORY_SEPARATOR;
            }

            $filepath = str_replace($default_rates, DIRECTORY_SEPARATOR, $filepath);
        }
        
        if ($full)
        {
            $filepath .= $this->get('file');
        }

        return $filepath;
    }

    public function downloadName()
    {
        $name = $this->get('name');

        $rate = (int)$this->get('rates');

        if (!empty($rate))
        {
            $name .= '_' .$rate .'hz';
        }

        $name .= '_' .time();

        $name .= '.' .pathinfo($this->get('file'), PATHINFO_EXTENSION);

        return html::token($name);
    }

    public function contentType()
    {
        switch ($this->get('type'))
        {
            case 'wav':
                return 'audio/x-wav';

            case 'ogg':
                return 'audio/ogg';

            case 'mp1':
            case 'mp3':
                return 'audio/mpeg';

            default:
                return file::mime($this->filepath(TRUE));
        }
    }

    public static function catalog($display = '%2$s (%4$s)')
    {
        $catalog = array();

        $records = Doctrine::getTable(__CLASS__)->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($records as $record)
        {
            $param_arr = arr::merge(array($display), $record);

            $param_arr = array_values($param_arr);

            $catalog[$record['mediafile_id']] =
                call_user_func_array('sprintf', $param_arr);
        }

        return $catalog;
    }
}