<?php defined('SYSPATH') or die('No direct access allowed.');
/*
* FreePBX Modular Telephony Software Library / Application
*
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
*
* Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
* express or implied. See the License for the specific language governing rights and limitations under the License.
*
* The Original Code is FreePBX Telephony Configuration API and GUI Framework.
* The Original Developer is the Initial Developer.
* The Initial Developer of the Original Code is Darren Schreiber
* All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
*
* Contributor(s):
*
* Karl Anderson
*
*/
/**
 * freepbx.php - Base FreePbx Controller
 *
 * This controller does a tremendous amount of work in stringing together plugins for processing form posts, validation,
 * user validation and more.
 *
 * See the manual at http://freepbx.org/v3/trac/ to learn more on how to utilize the features of FreePbx's controller class.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
abstract class FreePbx_Controller extends Template_Controller
{
    /**
     *
     * @var float The freepbx core version
     */
    public static $version = '3.0RC1';
    /*************************
    * AUTH RELATED SETTINGS *
    *************************/
    /**
     *
     * @var array An array of actions that do not require a logged in user. By default, all actions require a logged in user
     */
    protected $noAuth = array(); // Add actions for a controller to this array to prevent login being required
    
    /**
     * Automatically load the user based on a session variable. This must be off when the database does not exist.
     * @var boolean
     */
    public $autoloadUser = TRUE;
    /**********************
    * VIEW RELATED ITEMS *
    **********************/
    /**
     * Parameters for setting up our view. This includes the view's name, it's location and the template name. You can optionally
     * set a folder name (for skins, for example).
     *
     * @var array
     */
    public $viewParams = array();
    /**
     * When this is set, XML or JSON generation results in the rendering of datasets referenced by this variable.
     * If not set, the baseModel object is used (if set) to render API data.
     *
     * @var array Can be a multi-dimensional array
     */
    public $apiData = NULL;
    /*************************
    * DATA RELATED SETTINGS *
    *************************/
    /**
     * The name of the base model that this class is likely to reference. For example, if you are making a devicemanager module,
     * your base model is probably 'Device'. Must be capitalized in Doctrine form and match an existing model.
     *
     * This is used to figure out how to automatically parse and save your data in the doSave() method.
     * @var string
     */
    protected $baseModel;
    /**
     *
     * @var Validation A validator object for validating form data. This is global to this controller and can be used by plugins.
     */
    public static $validation;
    /**
     * Array of model field names that can be shown to users in data requests
     *
     * This variable tells the controller's automated magic methods whether or not fields should be publicly
     * readable/accessible. It is used in the scrubbing and processing of forms as well as in the display
     * of XML/JSON and other data formats that are automatically rendered.
     *
     * WARNING: If this variable is NOT set, it is presumed that any field that is writable is also readable.
     * If no field is explicitly marked as writable, it is assumed that ALL fields are read/write
     *
     * @var array
     */
    protected $readable;
    /**
     * Array of model field names that can be submitted by a form or API/XML/JSON/etc. request for recording to the database
     *
     * This variable tells the controller's automated magic methods whether or not fields should be publicly
     * writable. It is used in the scrubbing and processing of forms as well as in the processing
     * of XML/JSON and other data formats that are automatically saved or otherwise utilized.
     *
     * WARNING: If this variable is NOT set, it is assumed that whatever is readable is also writable (and if readable is NULL, everything is assumed read/write)
     *
     * @var array
     */
    protected $writable;
    /*******************
    * PRIVATE METHODS *
    ******************/
    /**
     * Setup parameters specific to this website (i.e. the URL entered), such as the relevant skin and/or permissions.
     * Also run an event that allows plugins to setup specifics based on the current skin or permissions.
     *  (This can be useful, for example, for allowing admin rights to people on your local LAN automatically)
     */
    private function __setupWebsite()
    {
        // Run any setup routines for plugins that need to run before the controller executes. We pass a reference to this controller for convenience.
        Event::run('freepbx.setup', $this);
    }
    /**
     * Our constructor. Here, we setup our template controller, including the general layout, any skins we're using, and so on.
     * We also load the current logged in user, setup CSS and JavaScript includes, and add a few hooks for generating our page.
     * Last modified by K Anderson 06-07-09
     *
     */
    /*******************
    * PUBLIC METHODS *
    ******************/
    public function __construct()
    {
        /*****************
        * GENERAL SETUP *
        *****************/
        // Guess at the baseModel name if none has been set. Use the controller's basename as the guess
        if (!$this->baseModel) {
            $this->baseModel = ucfirst(str_replace('_Controller', '', get_class()));
            // TODO: We could get fancy here and go seek out model names from the models directory if the class does not exist
            
        }

        // Assume that if nothing explicitly was specified as readable, then whatever is writable is also readable
        if (!$this->readable) {
            $this->readable = $this->writable;
        }
        
        // Instantiate sessions
        $this->session = Session::instance();

        // Instantiate internationalization
        $this->i18n = i18n::instance();
        
        /**
         * TODO: Remove this when i18n is more stable
         */
        if (!empty($_POST['lang'])) {
            if (empty(i18n::$langs[$_POST['lang']])) die();
            $this->session->set('lang', $_POST['lang']);
            echo i18n::$langs[$_POST['lang']];
            die();
        }
        // Setup anything related to this website's pages rendering
        $this->__setupWebsite();
        
        // Get current user
        // TODO: Are we still going to use this? This used to automatically make user vars available, but that's now abstracted into the user helper...
        //if ($this->autoloadUser) {
        //}

        // Go grab the logged in user if they exist. If they don't, see if anonymous access is allowed to this action. If not, redirect to login page
        if(FreePbx_Core::is_installing()) {
            Kohana::config_set('core.require_login', FALSE);
        } else if (Kohana::config('core.require_login') && !(is_array($this->noAuth) && in_array(Router::$method, $this->noAuth))) {
            if (!users::getCurrentUser()) {
                $this->session->set("requested_url", "/" . url::current()); // this will redirect from the login page back to this page/
                url::redirect('user/login');
            }
        }

        /*******************
        * RENDERING SETUP *
        *******************/
        // For safety, fail back to HTML if this is not set
        if (!defined('CONTENT_TYPE')) {
            // take all the URL elements used for routing and explode on /
            $pathParts = explode('/', Router::$current_uri);
            $content_type = 'html';
            foreach ($pathParts as $part) {
                // see if there is an extension on each part
                $extension = pathinfo($part, PATHINFO_EXTENSION);
                // if we find a html, json, xml extension then save it, keeping the last found extension
                if (!empty($extension) && in_array(strtolower($extension), array('html', 'json', 'xml'))) {
                    $content_type = strtolower($extension);
                }
            }
            define('CONTENT_TYPE', $content_type);
        }
        
        // Create a static validator, if one does not already exist. By default we populate it with post variables
        // This will hold all errors that occur within Doctrine and provides easy access for the controller to grab those errors
        // Note carefully that this is intentionally a singleton - Doctrine does not otherwise know which controller is associated
        // with which record. This implies that your errors will get stacked up on top of each other, but since it's one controller
        // per run of Kohana, we should be OK here. You can also use Kohana's validation class methods here, too.
        // FIXME: This should be moved!!!
        self::$validation = new Validation($_POST);
        // Set the default template, viewName and failback view to use, based on the content type. NOTE: It's perfectly fine to override this in the template setup hooks
        switch (CONTENT_TYPE) {
        case 'json':
            $this->viewParams['template'] = 'json/layout';
            $this->viewParams['name'] = Router::$controller . '/json/' . Router::$method;
            $this->viewParams['fallback'] = '{ error:"No Content" }'; // FIXME: Come on now... Make this valid XML markup
            break;

        case 'xml':
            $this->viewParams['template'] = 'xml/layout';
            $this->viewParams['name'] = Router::$controller . '/xml/' . Router::$method;
            $this->viewParams['fallback'] = '<xml><error>No Content</error></xml>'; // FIXME: Come on now... Make this valid XML markup
            break;

        default:
            if (request::is_ajax()) {
                $this->viewParams['template'] = 'ajax';
            } else {
                $this->viewParams['template'] = 'layout';
            }
            $this->viewParams['name'] = Router::$controller . '/' . Router::$method;
            $this->viewParams['fallback'] = '<BR><B>' . __('ERROR') . ':</B>' . __('There is no viewable content for this page.');
            break;
        };
        // NOTE: You can optionally set $viewFolder here to try an alternate folder for all views. If the view doesn't exist, $viewFolder is ignored
        $this->viewParams['folder'] = '';
        // Call all setup hooks related to content type.
        Event::run('template.' . CONTENT_TYPE, $this);

        /********************
        * PREPARE TEMPLATE *
        ********************/
        $this->template = $this->viewParams['folder'] . $this->viewParams['template'];
        // Call our parent controller's constructor
        // NOTE: This prepares our view and converts $this->template into an object . Changes to $this->viewParams['template'] are ignored past this point
        parent::__construct();

        /*******************
        * PREPARE CONTENT *
        *******************/
        if (CONTENT_TYPE == 'html') {
            // Initialize default HTML content
            $this->template->css = '';
            $this->template->js = '';
            $this->template->meta = '';
            $this->template->header = '';
            $this->template->footer = '';
            // Set the page title
            // TODO: Be more creative here?
            $this->template->title = ucfirst(Router::$controller);
            if (Router::$method != 'index') { // Index is always a boring name, don't use it (but use everything else)
                $this->template->title.= ' -> ' . ucfirst(Router::$method);
            }
        }

        // Call the constructor's for all plugins registered
        plugins::construct();
        try {
            $this->template->content = new View($this->viewParams['folder'] . $this->viewParams['name']);
        }
        catch(Exception $e) {
            try {
                $this->template->content = new View($this->viewParams['name']);
            }
            catch(Exception $e) {
                $this->template->content = new View();
                $this->template->body = $this->viewParams['fallback'];
            }
        }

        /********************
        * FINALIZE CONTENT *
        ********************/
        // Make it easier to get to and remember how to access the core view by setting up a reference to the template, called view
        $this->view = & $this->template->content;

        // Make it easier to access error information
        $this->errors = & self::$validation;
    }

    /**
     * Returns the validation errors
     * 
     * @return array
     */
    public function errors()
    {
        return FreePbx_Controller::$validation->errors();
    }

    /**
     * Scrub the contents of a form submission and return only public variables, identified by this controller's $writable array
     * The purpose of this function is to make sure people don't try and post data that they aren't allowed to post
     *
     * @param array $fieldPrefix Prefix for all field names. Usually an array name
     * @return array
     */
    private function scrubForm($fieldPrefix = NULL)
    {
        $form = $this->input->post($fieldPrefix);
        $scrubbed = array();
        if (is_array($this->writable)) foreach($this->writable as $fieldName) {
            if (isset($form[$fieldName])) $scrubbed[$fieldName] = $form[$fieldName];
        }

        return $scrubbed;
    }

    /**
     * Performs population of model,
     *
     * @param object The base object to save
     * @param string The message to display if successful
     * @param array An array of custom events to call during save
     * @return bool
     */
    protected function formSave(&$object, $saveMessage = NULL, $saveEvents = array())
    {
        // Copy any posted values from the POST to the model, after scrubbing for allowed posted fields
        if (get_parent_class($object) == 'FreePbx_Record') {
            $object->fromArray($this->scrubForm(strtolower(get_class($object))));
        } else {
            $object->fromArray($this->scrubForm(strtolower(get_parent_class($object))));
        }

        // Save data and all relations
        try {
            // Allow plugins to process any form-related data we just got back and attach to our data object
            plugins::save($this, $saveEvents);
            // Save this base record
            //kohana::log('debug', 'Saving: ' . print_r($object->toArray(), true));
            $object->save();

            if ($this->input->post('containsAssigned')) //hidden input to let us know the page needs to have assigned numbers processed
            {
                $primaryKeyCol = $object->_table->getIdentifier(); //get the column name, like confernce_id
                $foreign_id = $object->$primaryKeyCol; //get the keuy value
                if (get_parent_class($object) == 'FreePbx_Record') $class_type = get_class($object) . 'Number'; //transform to class name
                else $class_type = get_parent_class($object) . 'Number'; //transform to original parent's class name
                if (is_null($this->input->post('_numbers'))) //if te
                {
                    numbering::updateAssignment($class_type, $foreign_id, NULL);
                } else {
                    numbering::updateAssignment($class_type, $foreign_id, $_POST['_numbers']['assign']);
                }
            }

            plugins::save($this, array(
                'custom' => Router::$controller . '.success',
                'coreAction' => FALSE,
                'core' => FALSE
            ));
            // Success - display a custom save message, or a generalized one using the class name
            if ($saveMessage) {
                message::set($saveMessage, array(
                    'type' => 'success'
                ));
            } else {
                message::set(get_class($object) . ' saved!', array(
                    'type' => 'success'
                ));
            }
            return TRUE;
        }
        catch(Doctrine_Connection_Exception $e) {
            message::set('Doctrine error: ' . $e->getMessage());
        }
        catch(FreePbx_Exception $e) {
            kohana::log('alert', $e->getMessage());
            // Note that $this->view->errors is automatically populated by FreePbx_Record
            message::set('Please correct the errors listed below.');
        }
        catch (Exception $e) {
            message::set($e->getMessage());
        }
        // Tell other plugins that we want to repopulate form fields with the invalid data
        $this->repopulateForm = $this->input->post();
        return FALSE;
    }

    /**
     * Return the baseModel
     * 
     * @return sting
     */
    public function getBaseModel()
    {
        return $this->baseModel;
    }

    /**
     * scrub a XML response of all private or protected data
     *
     * @param array The data to be scrubbed
     * @param sting ask pyite.... 
     * @param array The array of valid public entries to allow in $arr
     * @return array
     */
    private function outputXmlIterator($arr, $level, $readable)
    {
        $xml = '';
        foreach($arr as $k => $v) {
            if (is_array($v)) {
                if (is_int($k)) {
                    $xml.= $this->outputXmlIterator($v, $level, (isset($readable[$k]) ? $readable[$k] : NULL));
                } else {
                    $tmp = $this->outputXmlIterator($v, $level + 1, (isset($readable[$k]) ? $readable[$k] : NULL));
                    if ($tmp) {
                        $xml.= str_pad(' ', $level) . '<' . strtolower($k) . '>';
                        $xml.= "\n" . $tmp . str_pad(' ', $level);
                        $xml.= "</" . strtolower($k) . ">\n";
                    }
                }
            } else if ((!$readable) or (in_array($k, $readable))) {
                $xml.= str_pad(' ', $level) . '<field id="' . $k . '">' . $v . "</field>";
            }
        }
        return $xml;
    }

    /**
     * generates valid XML markup from $arr, in root $rootName and
     * with only entries in $readable
     *
     * @param array The data values to generate XML from
     * @param string The XML root name to wrap it in
     * @param array The array of valid public entries to allow
     * @return XML
     */
    private function outputXml($arr, $rootName, $readable)
    {
        //$tmp = $this->createXmlArray($obj);
        $xml = "<" . $rootName . ">\n" . $this->outputXmlIterator($arr, 1, $readable) . "</" . $rootName . ">\n";
        return $xml;
    }

    /**
     * create a response for a XML request
     *
     * @return XML
     */
    public function renderXml()
    {
        header('Content-Type: text/xml');
        $data = array();
        $base = strtolower($this->getBaseModel());
        if ($this->apiData) {
            $data[$base] = $this->apiData;
        } else {
            // Use the base model & any loaded related models as the data set.
            if (isset($this->$base)) {
                $data[$base] = $this->$base->toArray();
            } else {
                $data[$base] = array();
            }
        }
        $data['System']['session_id'] = $this->session->id();
        $readable['System'] = 'session_id'; // This is always available
        $readable[$base] = $this->readable;
        echo $this->outputXml($data, 'data', $readable);
    }

    /**
     * scrub a JSON response of all private or protected data
     *
     * @param array The data to be scrubbed
     * @param array The array of valid public entries to allow in $arr
     * @return array
     */
    private function jsonScrubber($arr, $readable)
    {
        $scrubbed = array();
        foreach($arr as $k => $v) {
            if (is_array($v)) {
                if (is_int($k)) {
                    // we are just looping through a list of records - don't go up a level in $readable
                    $scrubbed[$k] = $this->jsonScrubber($v, $readable);
                } else {
                    $scrubbed[$k] = $this->jsonScrubber($v, (isset($readable[$k]) ? (array)$readable[$k] : array()));
                }
            } else if ((!$readable) or (in_array($k, $readable))) {
                $scrubbed[$k] = $v;
            }
        }
        return $scrubbed;
    }

    /**
     * create a response for a JSON request
     *
     * @return JSON
     */
    public function renderJson()
    {
        //header ('Content-Type: application/json');
        $data = array();
        $base = strtolower($this->getBaseModel());
        if ($this->apiData) {
            $data = $this->apiData; // When apiData is set manually we presume all fields are readable
            $readable = array();
        } else {
            // Use the base model & any loaded related models as the data set.
            if (isset($this->$base)) {
                $data[$base] = $this->$base->toArray();
                $readable[$base] = $this->readable;
                $readable['System'] = array(
                    'session_id'
                ); // This is always available
                
            } else {
                $data[$base] = array();
            }
        }
        $data['System']['session_id'] = $this->session->id();
        $data = $this->jsonScrubber($data, $readable);
        echo json_encode($data);
    }

    /**
     * This function preforms the standard delete, which is transactional
     * and calls all plugins.
     *
     * @param string the baseModel row id to delete
     * @param bool if true skip prompting for confirmation and delete immediately
     * @return bool
     */
    protected function stdDelete($id = NULL, $confirm = NULL, $options = array())
    {
        if ( !is_array($options) ) {
            $options = array('name_column' => $options);
        }
        
        $options += array(
            'view' => new View('generic/delete'),
            'title' => 'Delete ' . $this->baseModel,
            'err_redirect' =>  Router::$controller,
            'redirect' => Router::$controller,
            'name_column' => 'name'
        );


        // Overload the update view
        $this->template->content = $options['view'];
        $this->view->title = $options['title'];

        // If delete is called with no $id produce and error and stop
        if (is_null($id)) {
            message::set('Unable to process delete request, invalid entry point!', array(
                'redirect' => $options['err_redirect']
            ));
            return TRUE;
        }
        // Wrap the doctrine query just incase it throws
        try {
            //Wow, really need to use conservative fetching here and also andWhere(user_id) for security
            $row = Doctrine::getTable($this->baseModel)->find($id);
            // If we were unable to find a row by this id produce an error and stop
            if (!$row) {
                message::set('Unable to process delete request, can not locate ' . strtolower($this->baseModel) . ' ' . $id . '!', array(
                    'translate' => FALSE,
                    'redirect' => $options['err_redirect']
                ));
                return TRUE;
            }
        } catch(Exception $e) {
            $error = __('Error during database operation!');
            $error.= '<div><small>' . __($e->getMessage()) . '</small></div>';
            message::set($error, array(
                'translate' => FALSE,
                'redirect' => $options['err_redirect']
            ));
            return TRUE;
        }
        // check if this delete has been confirmed
        if ($this->input->post('confirm', FALSE) || $confirm === TRUE) {
            // Bring out the Gimp.
            $conn = Doctrine_Manager::connection();
            try {
                FreePbx_Record::setBaseSaveObject($row);
                // Gimp's sleeping.
                $conn->beginTransaction();
                plugins::delete($row);
                $row->delete();
                // Well, I guess you're gonna have to go wake him up now, won't you?
                $conn->commit();
                plugins::delete($row, array(
                    'custom' => Router::$controller . '.success',
                    'coreAction' => FALSE,
                    'core' => FALSE
                ));
                FreePbx_Record::setBaseSaveObject(NULL);
                message::set($this->baseModel . ' removed', array(
                    'type' => 'success',
                    'redirect' => $options['redirect']
                ));
                return TRUE;
            } catch(Exception $e) {
                $conn->rollback();
                $error = __('Failed to remove ' . strtolower($this->baseModel) . '!');
                $error .= '<div><small>' . __($e->getMessage()) . '</small></div>';
                message::set($error, array(
                    'translate' => FALSE,
                    'redirect' => $options['err_redirect']
                ));
                return FALSE;
            }
        } else if ($this->input->post('cancel', FALSE) || $confirm === FALSE) {
            // If the confirm form was submitted by any other method, ignore it
            message::set('Delete Cancelled', array(
                'type' => 'info',
                'redirect' => $options['redirect']
            ));
            return TRUE;
        }

        $this->view->baseModel = strtolower($this->baseModel);
            $name = $options['name_column'];
            if (isset($row[$name])) {
                $this->view->name = '\'' . $row[$name] .'\'';
            } else {
                $this->view->name = ' id ' .$id;
            }
    }

    public function submitted($cancel = NULL, $options = array()) {
        if (is_null($cancel)) {
            $cancel = Router::$controller;
        }
        
        $options += array (
            'submitString' => 'save',
            'cancelString' => 'cancel',
            'requestVar' => 'submit',
            'cancelURL' => $cancel
        );

        if (!empty($options['requestVar'])) {
            if (empty($_REQUEST[$options['requestVar']])) {
                return FALSE;
            } else {
                $requestVar = $_REQUEST[$options['requestVar']];
            }
            if (strcasecmp($requestVar, $options['cancelString']) == 0) {
                if (!empty($options['cancelURL'])) {
                    url::redirect($options['cancelURL']);
                } else {
                    return NULL;
                }
            }
            if (strcasecmp($requestVar, $options['submitString']) == 0) {
                return TRUE;
            }
            return FALSE;
        }

        if (sizeof($this->input->post()) != 0) {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * This function process the render, catching the output
     * and adding any jquery assets where necessary
     *
     * @return void
     */
    public function _render()
    {
        if ($this->auto_render == TRUE) {
            // If XML, render it
            if (CONTENT_TYPE == 'xml') {
                echo $this->renderXml();
            } elseif (CONTENT_TYPE == 'json') {
                // Render the template when the class is destroyed
                //$output = $this->template->render(false);
                echo $this->renderJson();
            } else {
                // Add some makers so we now where to place jquery assets
                $this->template->js .= "\n{js}";
                $this->template->css .= "\n{css}";
                // Render the template when the class is destroyed
                $output = $this->template->render(false);
                // Initialize a default jquery assets array so the implodes dont explode :)
                $assets = array(
                    'js' => array(),
                    'css' => array()
                );
                // Run an event calling jquery helper and updating the $jqueryAssets with assets
                Event::run('system.post_template', $assets);

                // Put the new assets into the output where we mark before
                $output = str_replace(array(
                    '{js}',
                    '{css}'
                ) , array(
                    implode($assets['js'], "\n"),
                    implode($assets['css'], "\n")
                ) , $output);
                // Echo the output, this is the original behavor of _render()
                echo $output;
            }
        }
    }
}
