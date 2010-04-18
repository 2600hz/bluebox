<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * EndpointDriver.php - This class provides support for endpoint phones
 *
 * @author Michael Phillips
 * @author Karl Anderson
 * @license MPL
 * @package FreePBX3
 */
abstract class EndpointDriver
{
    public $mac = '';
    protected $parameters = NULL;
    protected $directories = array();
    protected $files = array();
    protected $provisionPath = NULL;
    protected $pendingDeletes = NULL;
    /**
     * This function maps the doctrine record EndpointLine into meaningfull
     * keys in the parameters array.  Used by the template engine when
     * producing files for a line, it will be called foreach line each
     * time a line file is produced. Each time $line will be the EndpointLine
     * doctrine object choosen by the user for the $lineNumber being processed.
     *
     * @param object The doctrine record EndpointLine object currently being used to produce files
     * @param string The line number currently being produced
     * @return array THIS MUST RETURN AN ARRAY!
     */
    abstract protected function mapEndpointToLine($endpointLine, $options);
    /**
     * This function maps the doctrine record EndpointLine into meaningfull
     * keys in the parameters array.  Used by the template engine when
     * producing files for a line, it will be called for each line with a
     * differing doctrine object.
     *
     * @param object The doctrine record Endpoint object currently being used to produce files
     * @return array THIS MUST RETURN AN ARRAY!
     */
    abstract protected function mapEndpointToPhone($endpoint, $options);
    /**
     * The constructor
     *
     * @param string A well formated mac address of the endpoint
     * @return void
     */
    public function __construct($mac)
    {
        $mac = preg_replace('/[^0-9a-fA-F]*/', '', $mac);
        $this->mac = $mac;
        kohana::log('debug', "Created new provisioner container for mac $mac");
        $q = Doctrine_Query::create()->select('es.provision_path')->from('EndpointSetting es')->where('provision_path <> ?', '');
        $provisionPath = $q->execute(array() , Doctrine::HYDRATE_ARRAY);
        $provisionPath = reset($provisionPath);
        if (!empty($provisionPath)) {
            kohana::log('debug', 'Found and set provision path to ' . $provisionPath['provision_path']);
            $this->provisionPath = $provisionPath['provision_path'];
        } else {
            kohana::log('debug', 'Could not find a provision path, all files dynamic');
        }
    }
    /**
     * The import function is used to reload an endpoint driver
     *
     * @param object Doctrine endpoint record object
     * @return void
     */
    public function import($parameters = NULL)
    {
        $this->parameters = $parameters;
        if (!empty($parameters->mac)) {
            $mac = preg_replace('/[^0-9a-fA-F]*/', '', $parameters->mac);
            $this->mac = $mac;
        }
        kohana::log('debug', "Loaded parameters for mac " . $this->mac);
    }
    public function refresh()
    {
        kohana::log('debug', "Refreshing parameters for mac " . $this->mac);
        $this->parameters->refresh(true);
        foreach($this->files as $file => $options) {
            if (isset($options['composed'])) {
                unset($this->files[$files]['composed']);
            }
        }
    }
    /**
     * getParameters returns the doctrine endpoint record object for external
     * access
     *
     * @return object Doctrine_record
     */
    public function getEndpoint()
    {
        return $this->parameters;
    }
    /**
     * Get the mac address of this endpoint
     *
     * @return string|NULL
     */
    public function mac($makePretty = FALSE)
    {
        if (!empty($this->mac)) {
            if ($makePretty && strlen($this->mac) == 12) {
                return preg_replace('/([0-9a-fA-F]{2})(?!$)/', '$1:', $this->mac);
            } else {
                return $this->mac;
            }
        }
        return NULL;
    }
    /**
     * Get the endpoint id
     *
     * @return string|NULL
     */
    public function id()
    {
        if (!empty($this->parameters['endpoint_id'])) {
            return $this->parameters['endpoint_id'];
        }
        return NULL;
    }
    /**
     * Returns the vendor name of this endpoing
     *
     * @return string
     */
    public function vendor()
    {
        return EndpointManager::getVendorName($this->parameters->EndpointModel['endpoint_vendor_id']);
    }
    /**
     * Returns the model name of this endpoint
     *
     * @return string
     */
    public function model()
    {
        return EndpointManager::getModelName($this->parameters['endpoint_model_id']);
    }
    /**
     * Returns the doctrine endpoint parameters, if they exists
     *
     * @return array|bool
     */
    public function getParameters()
    {
        if (isset($this->parameters['parameters'])) {
            return array(
                'parameters' => $this->parameters['parameters']
            );
        }
        return array(
            'parameters' => array()
        );
    }
    /**
     * Returns the max number of lines this model supports
     *
     * @return string
     */
    public function lineCount()
    {
        return $this->parameters->EndpointModel['line_count'];
    }
    /**
     * This returns the doctrine record for a line
     *
     * @param int The line appereance number (starting at 0)
     * @return object|false
     */
    public function getLine($lineNumber)
    {
        if (isset($this->parameters['EndpointLine'][$lineNumber])) {
            return $this->parameters['EndpointLine'][$lineNumber];
        }
        return FALSE;
    }
    /**
     * Get an array of all the line assignment parameters
     *
     * @return array
     */
    public function getLines()
    {
        $lines = array();
        foreach($this->parameters['EndpointLine'] as $line) {
            // This attempts to us the line_appearance as the key
            // which which will later be used to sort the array.
            // If there is a collision it is first come, first serve
            if (!isset($lines[$line->line_appearance])) {
                $ptrLines = & $lines[$line->line_appearance];
            } else {
                $ptrLines = & $lines[];
            }
            // load the line parameters
            $ptrLines['id'] = $line->endpoint_line_id;
            $ptrLines['parameters'] = (array)$line->parameters;
            // Get all the device parameters
            $ignore = array(
                'user_id',
                'class_type',
                'foreign_id',
                'created_at',
                'updated_at'
            );
            $parameters = $line->Device->toArray(false);
            $parameters = array_diff_key($parameters, array_flip($ignore));
            $parameters['type'] = str_ireplace('Device', '', $line->Device->class_type);
            $ptrLines+= $parameters;
            // This is repsponisble for all getting the registration data
            // and will need to be udated per device type
            switch ($line->Device->class_type) {
            case 'SipDevice':
                $ignore = array(
                    'created_at',
                    'updated_at'
                );
                $parameters = $line->Device->Sip->toArray(false);
                $parameters = array_diff_key($parameters, array_flip($ignore));
                $ptrLines+= $parameters;
                break;

            default:
                kohana::log('error', "Attempted to load a unknown device class_type " . $line->Device->class_type);
                throw new Exception("Provisioner can not handle device type " . $ptrLines['type']);
                break;
            }
            //Get all the user parameters
            $ignore = array(
                'location_id',
                'password',
                'logins',
                'last_login',
                'password_reset_token',
                'created_at',
                'updated_at',
                'last_logged_ip'
            );
            $parameters = $line->Device->User->toArray(false);
            $parameters = array_diff_key($parameters, array_flip($ignore));
            $ptrLines+= $parameters;
            //Get all the location parameters
            $ignore = array(
                'created_at',
                'updated_at'
            );
            $parameters = $line->Device->User->Location->toArray(false);
            $parameters = array_diff_key($parameters, array_flip($ignore));
            $ptrLines+= $parameters;
        }
        sort($lines);
        return $lines;
    }
    /**
     * Remove any flat files that exist for this endpoint
     *
     * @return bool
     */
    public function deleteFiles($pendUntilCreate = FALSE, $filesOnly = TRUE, $attemptShared = FALSE)
    {
        // if there is no provisionPath then there is no where to delete from
        if (is_null($this->provisionPath)) {
            return FALSE;
        }

        // we need to cache our shared resource status to save time....
        $sharedResources = NULL;

        //list all the files for this endpoint
        $files = $this->composeFiles(FALSE);
        foreach($files as $file => $options) {
            // default options for file creation
            $options+= array(
                'filePermission' => 0755,
                'update' => FALSE
            );

            // if this is a shared file
            if (!empty($options['shared'])) {
                // see if we are allowed to determine if these can be deleted
                if (empty($attemptShared)) continue;
                // if we already know that this is shared skip figuring it out
                if (is_null($sharedResources)) {
                    // find all endpoints with the same vendor id
                    $model = EndpointManager::getModel($this->parameters['endpoint_model_id']);
                    if (empty($model['endpoint_vendor_id'])) {
                        $sharedResources = TRUE;
                        break;
                    }
                    $models = EndpointManager::listModels($model['endpoint_vendor_id'], array(), FALSE);
                    $q = Doctrine_Query::create()->
                        select('e.endpoint_id')->
                        from('Endpoint e')->
                        whereIn('e.endpoint_model_id', array_keys($models));
                    $results = $q->execute(array() , Doctrine::HYDRATE_ARRAY);

                    $sharedResources = FALSE;
                    foreach ($results as $endpoint) {
                        if ($endpoint['endpoint_id'] != $this->parameters['endpoint_id']) {
                            kohana::log('debug', 'Skipping shared resource (' . $file . ') because this vendor has other endpoints');
                            $sharedResources = TRUE;
                            break;
                        }
                    }
                }
                // test if this is shared
                if(!empty($sharedResources)) continue;
            }

            // piece together our full file name
            $filePath = rtrim($this->provisionPath, '/') . '/' . ltrim($file, '/');

            if (!empty($pendUntilCreate)) {
                $this->pendingDeletes[$file] = $filePath;
            } else {
                if (filesystem::delete($filePath)) {
                    kohana::log('debug', 'Removed provisioner file ' . $file);
                } else {
                    kohana::log('error', 'Failed to removed provisioner file ' . $file);
                }
            }
        }
       

        if (!empty($filesOnly)) {
            return TRUE;
        }
        // list all the directories for this endpoint
        $directories = $this->composeDirectories(FALSE);
        foreach($directories as $directory => $options) {
            // default options for directory creation
            $options+= array(
                'folderPermission' => 0755,
                'update' => FALSE
            );

            // if this is a shared file
            if (!empty($options['shared'])) {
                // see if we are allowed to determine if these can be deleted
                if (empty($attemptShared)) continue;
                // if we already know that this is shared skip figuring it out
                if (is_null($sharedResources)) {
                    // find all endpoints with the same vendor id
                    $model = EndpointManager::getModel($this->parameters['endpoint_model_id']);
                    if (empty($model['endpoint_vendor_id'])) {
                        $sharedResources = TRUE;
                        break;
                    }
                    $models = EndpointManager::listModels($model['endpoint_vendor_id'], array(), FALSE);
                    $q = Doctrine_Query::create()->
                        select('e.endpoint_id')->
                        from('Endpoint e')->
                        whereIn('e.endpoint_model_id', array_keys($models));
                    $results = $q->execute(array() , Doctrine::HYDRATE_ARRAY);

                    $sharedResources = FALSE;
                    foreach ($results as $endpoint) {
                        if ($endpoint['endpoint_id'] != $this->parameters['endpoint_id']) {
                            kohana::log('debug', 'Skipping shared resource (' . $file . ') because this vendor has other endpoints');
                            $sharedResources = TRUE;
                            break;
                        }
                    }
                }
                // test if this is shared
                if(!empty($sharedResources)) continue;
            }

            // piece together our directory name
            $folderPath = rtrim($this->provisionPath, '/') . '/' . trim($directory, '/');

            if (!empty($pendUntilCreate)) {
                $this->pendingDeletes[$directory] = $folderPath;
            } else {
                if (filesystem::delete($folderPath)) {
                    kohana::log('debug', 'Removed provisioner dir ' . $directory);
                } else {
                    kohana::log('error', 'Failed to removed provisioner dir ' . $directory);
                }
            }
        }

        return TRUE;
    }
    public function deletePending()
    {
        // if there are deletes pending prior to file creation then do those now
        if(!is_null($this->pendingDeletes)) {
            $pendingDeletes = array_unique($this->pendingDeletes);
            foreach($pendingDeletes as $relative => $absolute) {
                if (filesystem::delete($absolute)) {
                    kohana::log('debug', 'Cached delete of ' . $relative);
                } else {
                    kohana::log('error', 'Cached delete failed to removed ' . $relative);
                }
            }
            $this->pendingDeletes = NULL;
        }
    }
    /**
     * Create flat file to provision this endpoint
     *
     * @return bool
     */
    public function createFiles()
    {
        // if there is no provisionPath then there is no where to write to
        if (is_null($this->provisionPath)) {
            return FALSE;
        }
        $this->deletePending();
        // list all the directories for this endpoint
        $directories = $this->composeDirectories(TRUE);
        foreach($directories as $directory => $options) {
            // default options for directory creation
            $options+= array(
                'folderPermission' => 0755,
                'update' => FALSE
            );
            // piece together our directory name
            $folderPath = rtrim($this->provisionPath, '/') . '/' . trim($directory, '/');
            // if the directory has a template then create the directory, and copy in the template contents
            if (!empty($options['template'])) {
                kohana::log('debug', "Creating empty directory $directory");
                filesystem::createDirectory($folderPath, $options['folderPermission']);
                kohana::log('debug', "Copying content from ${options['template']} to $directory");
                filesystem::copy(MODPATH . $options['template'], $folderPath, $options['folderPermission'], $options);
            } else {
                // create an empty directory
                kohana::log('debug', "Creating empty directory $directory");
                filesystem::createDirectory($folderPath, $options['folderPermission']);
            }
        }
        //list all the files for this endpoint
        $files = $this->composeFiles(TRUE);
        foreach($files as $file => $options) {
            // default options for file creation
            $options+= array(
                'filePermission' => 0755,
                'update' => FALSE
            );
            // piece together our full file name
            $filePath = rtrim($this->provisionPath, '/') . '/' . ltrim($file, '/');
            kohana::log('debug', "Creating provisioning file $file");
            // if there is no composed array then populate it with an empty array (just incase)
            if (empty($options['composed'][$file])) {
                $options['composed'][$file] = array();
            }
            // if we didnt parse a template then just copy the file over
            if (isset($options['parse']) && $options['parse'] === FALSE) {
                filesystem::copy(MODPATH . $options['template'], $filePath, $options['filePermission'], $options);
            } else {
                // check if there is an existing file and if we can overwrite it if so
                if (file_exists($filePath)) {
                    if (isset($options['overwrite']) && $options['overwrite'] == FALSE) {
                        kohana::log('debug', "Skipping overwrite of " . $filePath);
                        continue;
                    }
                }
                // put our new file into the provisioning directory
                file_put_contents($filePath, $options['composed'][$file]);
            }
        }
    }
    /**
     * This function takes a path relative to the provisioner root and
     * return the file either for download or for display in HTML (where
     * appropriate)
     *
     * @param string A file path relative to provisioner root
     * @param bool If true the function returns a string suitable for HTML
     * @return mixed
     */
    public function getFile($path, $forHTML = FALSE)
    {
        $filesystem = array();
        $isFile = TRUE;
        $content = $file = NULL;
        if (is_null($this->provisionPath)) {
            // Check the files for $path
            $files = $this->composeFiles(FALSE);
            if (!empty($files[$path])) {
                // if the file is in this collection, then compile them so we can get it
                $files = $this->composeFiles();
                // If the file is empty handle that
                if (!empty($files[$path]['composed'][$path])) {
                    $content = $files[$path]['composed'][$path];
                    $content = implode('', $content);
                } else {
                    $content = '';
                }
                // This is in memory only (ie use $content)
                $isFile = FALSE;
            }
            // see if we found the file above
            if (is_null($content)) {
                // Check in the directories for the $path
                $directories = $this->composeDirectories(FALSE);
                if (empty($directories[$path])) {
                    // We have been avoiding this because it is the most expensive
                    $directories = $this->composeDirectories(TRUE);
                    foreach($directories as $directory => $options) {
                        if (!empty($options['composed'][$directory][$path])) {
                            $file = $options['composed'][$directory][$path];
                            break;
                        }
                    }
                    // if we cant find by now we never will
                    if (is_null($file)) {
                        kohana::log('error', "Unable to get $path");
                        if (!empty($forHTML)) return 'File not found';
                        else header("HTTP/1.0 404 Not Found");
                        die();
                    }
                } else {
                    $file = $directories[$path];
                }
            }
        } else {
            $file = rtrim($this->provisionPath, '/') . '/' . $path;
        }
        // Determine the appropriate method to return the file
        if (filesystem::is_binary($file, $content) && !empty($forHTML)) {
            kohana::log('debug', "Found $path as a binary file for HTML");
            $html = '<h2>' . $path . '</h2>';
            $html.= __('This is a binary file and can not be displayed.');
            $html.= '<a href="';
            $html.= url::site('provisioner/get') . '?type=binary&mac=' . $this->mac . '&file=' . html::specialchars(str_replace('/', '%2F', $path));
            $html.= '" class="flt-right">' . __('Click here to download.') . '</a>';
            return $html;
        } else if (!empty($forHTML)) {
            kohana::log('debug', "Found $path as a text file for HTML");
            $html = '<h2>' . $path . '</h2>';
            $html.= '<a href="';
            $html.= url::site('provisioner/get') . '?type=binary&mac=' . $this->mac . '&file=' . html::specialchars(str_replace('/', '%2F', $path));
            $html.= '" class="flt-right">' . __('Click here to download.') . '</a>';
            if ($isFile) {
                $content = file($file);
                $content = implode('', $content);
            }
            $html.= '<xmp>' . $content . '</xmp>';
            return $html;
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            if ($isFile) {
                header('Content-Length: ' . filesize($file));
            } else {
                header('Content-Length: ' . strlen($content));
            }
            ob_clean();
            flush();
            if ($isFile) {
                readfile($file);
            } else {
                echo $content;
            }
            kohana::log('debug', "Fulfilled request to get $path");
            exit;
        }
    }
    /**
     * This function will build an array of the directory tree
     * either from the filesystem or out of memory
     *
     * @return array
     */
    public function getTree()
    {
        $filesystem = array();
        if (is_null($this->provisionPath)) {
            // Add the directory structure
            $directories = $this->composeDirectories(FALSE);
            foreach($directories as $directory => $options) {
                if (!empty($options['template'])) {
                    $content = filesystem::directoryToArray(MODPATH . ltrim($options['template'], '/') , TRUE);
                } else {
                    $content = NULL;
                }
                $this->_decomposePath($directory, $filesystem, $content);
            }
            // Add the individual files
            $files = $this->composeFiles(FALSE);
            foreach($files as $file => $contents) {
                $this->_decomposePath($file, $filesystem, '');
            }
        } else {
            $directories = $this->composeDirectories(FALSE);
            foreach($directories as $directory => $options) {
                // piece together our directory name
                $fullDirectory = rtrim($this->provisionPath, '/') . '/' . trim($directory, '/');
                $content = filesystem::directoryToArray($fullDirectory, TRUE);
                $this->_decomposePath($directory, $filesystem, $content);
            }
            $files = $this->composeFiles(FALSE);
            foreach($files as $file => $contents) {
                $this->_decomposePath($file, $filesystem, '');
            }
        }
        return $filesystem;
    }
    /**
     * Process the directories array
     *
     * @param bool if false then a directories template will not be processed
     * @return array
     */
    public function composeDirectories($parseTemplate = TRUE)
    {
        $directories = array();
        // Add the directory structure
        foreach($this->directories as $directory => $options) {
            // Convience wrapper
            $ptContent = & $this->directories[$directory];
            // Replace the markers in the directory string
            $directoryName = str_replace(array(
                '{mac}',
                '{MAC}'
            ) , array(
                strtolower($this->mac) ,
                strtoupper($this->mac) ,
            ) , $directory);
            $directoryName = rtrim($directoryName, '/') . '/';
            if (!empty($parseTemplate) && !empty($options['template'])) {
                $dirToArryOptions = array(
                    'multidimensional' => FALSE,
                    'prependDirectory' => $directoryName
                );
                $this->directories[$directory]['composed'][$directoryName] = filesystem::directoryToArray(MODPATH . ltrim($options['template'], '/') , TRUE, $dirToArryOptions);
            }
            $directories[$directoryName] = & $this->directories[$directory];
        }
        return $directories;
    }
    /**
     * Process the files array
     *
     * @param bool if false then a files template will not be processed
     * @return array
     */
    public function composeFiles($parseTemplate = TRUE)
    {
        $files = array();
        foreach($this->files as $file => $options) {
            // Convience wrapper
            $options['ptFile'] = & $this->files[$file];
            $options['fileName'] = $file;
            $options['createdFiles'] = array_keys($files);
            if (!isset($options['foreach'])) {
                $options['foreach'] = 'phone';
            }
            switch ($options['foreach']) {
            case 'line':
                $files+= $this->_composeLineFiles($options, $parseTemplate);
                break;

            default:
                $files+= $this->_composePhoneFiles($options, $parseTemplate);
                break;
            }
        }
        return $files;
    }
    /**
     * This function will return an array with template files parsed for each
     * Endpoint
     *
     * @param array file options
     * @param bool if false the template will not be parsed
     * @return array
     */
    protected function _composePhoneFiles($options, $parseTemplate = TRUE)
    {
        // Determine the parsed file name
        $options['parsedName'] = str_replace(array(
            '{mac}',
            '{MAC}'
        ) , array(
            strtolower($this->mac) ,
            strtoupper($this->mac) ,
        ) , $options['fileName']);
        // if we dont need to parse the template return
        if (empty($parseTemplate) || empty($options['template'])) {
            if (empty($options['template'])) {
                kohana::log('debug', "File ${options['parsedName']} had an no template, producing an empty file");
                $options['ptFile']['composed'][$options['parsedName']] = array();
            }
            return array(
                $options['parsedName'] => & $options['ptFile']
            );
        }
        $template = $this->_getTemplate($options['template']);
        if (empty($template)) {
            kohana::log('debug', "File ${options['parsedName']} had an invalid template, producing an empty file");
            $options['ptFile']['composed'][$options['parsedName']] = array();
            return array(
                $options['parsedName'] => & $options['ptFile']
            );
        }
        if (isset($options['parse']) && $options['parse'] === FALSE) {
            kohana::log('debug', "File ${options['parsedName']} will not be parsed for markers");
            $options['ptFile']['composed'][$options['parsedName']] = $template;
            return array(
                $options['parsedName'] => & $options['ptFile']
            );
        }
        // Build the default phone parameters from the user
        $parameters = $this->parameters->toArray(FALSE);
        $tmp = $this->parameters['parameters'];
        unset($parameters['parameters']);
        unset($parameters['options']);
        $parameters+= $tmp;
        // Build the phone parameters in the device driver
        $parameters+= (array)$this->mapEndpointToPhone($this->parameters, $options);
        // Build a default set of parameters
        $parameters+= array(
            'mac' => strtolower($this->mac) ,
            'MAC' => strtoupper($this->mac) ,
        );
        // Produce and return this parsed template
        kohana::log('debug', "Composing phone file ${options['parsedName']}");
        $options['ptFile']['composed'][$options['parsedName']] = $this->_setTemplateMarkers($template, $parameters);
        return array(
            $options['parsedName'] => $options['ptFile']
        );
    }
    /**
     * This function will return an array with template files parsed for each
     * EndpointLine in associated with this endpoint
     *
     * @param array A template file to parse for each associated EndpointLine
     * @param string The template file name
     * @return array
     */
    protected function _composeLineFiles($options, $parseTemplate = TRUE)
    {
        $lineFiles = array();
        foreach($this->parameters['EndpointLine'] as $lineNumber => $line) {
            // Determine the file name and path
            $options['parsedName'] = str_replace(array(
                '{domain}',
                '{mac}',
                '{MAC}',
                '{line}'
            ) , array(
                $line->Device->User->Location->domain,
                strtolower($this->mac) ,
                strtoupper($this->mac) ,
                $lineNumber + 1
            ) , $options['fileName']);
            // if we dont need to parse the template return
            if (empty($parseTemplate) || empty($options['template'])) {
                $lineFiles[$options['parsedName']] = & $options['ptFile'];
                continue;
            }
            $template = $this->_getTemplate($options['template']);
            if (empty($template)) {
                $lineFiles[$options['parsedName']] = & $options['ptFile'];
                continue;
            }
            $options['lineNumber'] = $lineNumber + 1;
            // Build the default line parameters from the user
            $parameters = $line->toArray(FALSE);
            $tmp = $parameters['parameters'];
            unset($parameters['parameters']);
            $parameters+= $tmp;
            // Build the line parameters in the device driver
            $parameters+= (array)$this->mapEndpointToLine($line, $options);
            // Build a default set of parameters
            $parameters+= array(
                'domain' => $line->Device->User->Location->domain,
                'mac' => strtolower($this->mac) ,
                'MAC' => strtoupper($this->mac) ,
                'line' => $lineNumber + 1
            );
            // Produce and save this parsed template
            kohana::log('debug', "Composing line ${options['lineNumber']} file ${options['parsedName']}");
            $options['ptFile']['composed'][$options['parsedName']] = $this->_setTemplateMarkers($template, $parameters);
            $lineFiles[$options['parsedName']] = & $options['ptFile'];
        }
        return $lineFiles;
    }
    /**
     * Read the template at templatePath into an array.  All paths
     * are assumed to be relative to MODPATH
     *
     * @param string Path to tempate, relative to MODPATH
     * @return array
     */
    protected function _getTemplate($templatePath)
    {
        // Attempt to read the template into an array
        $template = @file(MODPATH . ltrim($templatePath, '/'));
        // If we got a template array return it
        if (!empty($template)) return $template;
        // Something has gone wrong!
        kohana::log('error', "Could not read template " . MODPATH . $templatePath);
        return array();
    }
    /**
     * Returns markers and their default values in a template set
     *
     * @param string The template set to retrieve
     * @param bool If true returns markers even if they have no default value
     * @param bool If true the root array keys are the template file paths
     * @return array
     */
    public function getMarkers($for = 'phone', $returnAll = TRUE, $organizeByFile = FALSE)
    {
        $defaults = array();
        foreach($this->files as $file => $options) {
            // Check if the template files is specified
            if (empty($options['template'])) {
                kohana::log('debug', "File " . $file . " has no defined template");
                continue;
            }
            if (isset($options['parse']) && empty($options['parse'])) {
                continue;
            }
            // The set we are looking to collect determines our actions
            switch ($for) {
            case 'phone':
                if (!empty($options['foreach']) && strtolower($options['foreach']) == 'phone') {
                    $template = $this->_getTemplate($options['template']);
                    if ($organizeByFile) {
                        $defaults[$file] = $this->_getTemplateMarkers($template, $returnAll);
                    } else {
                        $defaults+= $this->_getTemplateMarkers($template, $returnAll);
                    }
                }
                break;

            case 'line':
                if (!empty($options['foreach']) && strtolower($options['foreach']) == 'line') {
                    $template = $this->_getTemplate($options['template']);
                    if ($organizeByFile) {
                        $defaults[$file] = $this->_getTemplateMarkers($template, $returnAll);
                    } else {
                        $defaults+= $this->_getTemplateMarkers($template, $returnAll);
                    }
                }
                break;
            }
        }
        return $defaults;
    }
    /**
     * This function will parse a template file
     * and return a key value array of markers and their defaults
     *
     * @param array The template files an an array
     * @param bool Return markers even if they have no default settings
     * @return array
     */
    protected function _getTemplateMarkers($template, $returnAll = FALSE)
    {
        $markers = array();
        foreach($template as $line) {
            preg_match_all('/(\{([^}|]*)\|?([^}]*)\})/im', $line, $results, PREG_PATTERN_ORDER);
            // If this line of the template has no markers move to the next
            if (empty($results[1])) continue;
            // Foreach of the markers on this line loop through
            foreach($results[1] as $key => $search) {
                $marker = $results[2][$key];
                // If the marker has a default load it into the result array
                // or if we are returning all markers
                if (!empty($results[3][$key])) {
                    $markers[$marker] = $results[3][$key];
                } else if (!empty($returnAll)) {
                    $markers[$marker] = $results[3][$key];
                }
            }
        }
        return $markers;
    }
    /**
     * This function will parse a template file
     * replacing all occurances of our markers
     *
     * @param array $file An array of lines from a file
     * @param array $markers Key, value array of marker settings
     * @return array
     */
    protected function _setTemplateMarkers($template, $parameters)
    {
        foreach($template as $lineNum => $line) {
            preg_match_all('/(\{([^}|]*)\|?([^}]*)\})/im', $line, $results, PREG_PATTERN_ORDER);
            if (empty($results[1])) continue;
            foreach($results[1] as $key => $search) {
                $marker = $results[2][$key];
                if (isset($parameters[$marker])) $results[3][$key] = $parameters[$marker];
            }
            $template[$lineNum] = str_replace($results[1], $results[3], $line);
        }
        return $template;
    }
    /**
     * This function is used to make a path into a multidimensional array
     *
     * @param string $path
     * @param array $array
     * @param string|array $contents
     * @return void
     */
    protected function _decomposePath($path, &$array, $contents)
    {
        $ptArray = & $array;
        extract(pathinfo($path));
        // If the directory is defined as root then ignore it
        if ($dirname == '.') {
            $ptArray[$basename] = $contents;
            return;
        }
        // Path to multidemensional array
        $folders = explode('/', $dirname);
        $ptArray = & $ptArray[$folders[0]];
        unset($folders[0]);
        foreach((array)$folders as $folder) {
            $ptArray = & $ptArray[$folder];
        }
        $ptArray[$basename] = $contents;
    }
}
