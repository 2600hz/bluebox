<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core
 * @author     Darren Schreiber <d@d-man.org>
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
abstract class Bluebox_Controller extends Template_Controller
{
    const SUBMIT_CONFIRM = 'confirm';

    const SUBMIT_DENY = 'deny';

    /**
     * @var float The bluebox core version
     */
    public static $version = '1.0';

    protected $authBypass = array();

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
     * TODO: This is part of the silliest hack I have ever had to do,
     * see function post_template
     */
    public static $onPageAssets = array('js' => array(), 'css' => array());
    
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
        if (!$this->baseModel)
        {
            $this->baseModel = ucfirst(str_replace('_Controller', '', get_class()));
        }
        
        // Instantiate sessions
        $this->session = Session::instance();

        if (!request::is_ajax())
        {
            $this->session->set('ajax.base_controller', strtolower(Router::$controller));

            $this->session->set('ajax.base_method', strtolower(Router::$method));
        }
        
        // Instantiate internationalization
        $this->i18n = i18n::instance();

        /**
         * TODO: This is so nasty...
         */
        if (!empty($_POST['lang']))
        {
            if (empty(i18n::$langs[$_POST['lang']]))
            {
                die();
            }

            $this->session->set('lang', $_POST['lang']);

            echo i18n::$langs[$_POST['lang']];

            die();
        }

        // Create a static validator, if one does not already exist. By default we populate it with post variables
        // This will hold all errors that occur within Doctrine and provides easy access for the controller to grab those errors
        // Note carefully that this is intentionally a singleton - Doctrine does not otherwise know which controller is associated
        // with which record. This implies that your errors will get stacked up on top of each other, but since it's one controller
        // per run of Kohana, we should be OK here. You can also use Kohana's validation class methods here, too.
        // FIXME: This should be moved!!!
        self::$validation = new Validation($_POST);

        // Setup anything related to this website's pages rendering
        Event::run('bluebox.setup', $this);

        // Setup anything related to authenticating the user
        Event::run('bluebox.authenticate', $this);

        // Setup anything related to authorizing the user
        Event::run('bluebox.authorize', $this);

        /*******************
        * RENDERING SETUP *
        *******************/
        // For safety, fail back to HTML if this is not set
        if (!defined('CONTENT_TYPE'))
        {
            // take all the URL elements used for routing and explode on /
            $pathParts = explode('/', Router::$current_uri);

            $content_type = 'html';

            foreach ($pathParts as $part)
            {
                // see if there is an extension on each part
                $extension = pathinfo($part, PATHINFO_EXTENSION);

                // if we find a html, json, xml extension then save it, keeping the last found extension
                if (!empty($extension) && in_array(strtolower($extension), array('html', 'json', 'xml')))
                {
                    $content_type = strtolower($extension);
                }
            }

            define('CONTENT_TYPE', $content_type);
        }
                
        // Set the default template, viewName and failback view to use, based on the content type.
        // NOTE: It's perfectly fine to override this in the template setup hooks
        switch (CONTENT_TYPE)
        {
            case 'json':
                $this->viewParams['template'] = 'json/layout';

                $this->viewParams['name'] = Router::$controller . '/json/' . Router::$method;

                $this->viewParams['fallback'] = 'NO CONTENT';

                break;

            case 'xml':
                $this->viewParams['template'] = 'xml/layout';

                $this->viewParams['name'] = Router::$controller . '/xml/' . Router::$method;

                $this->viewParams['fallback'] = '<xml><error>No Content</error></xml>';

                break;

            default:
                if (request::is_ajax() OR !empty($_REQUEST['qtipAjaxForm']))
                {
                    $this->viewParams['template'] = 'ajax';
                } 
                else
                {
                    $this->viewParams['template'] = 'layout';
                }

                $this->viewParams['name'] = Router::$controller . '/' . Router::$method;

                $this->viewParams['fallback'] = 'ERROR: There is no viewable content for this page';

                break;
                
        };
        
        // NOTE: You can optionally set $viewFolder here to try an alternate folder for all views.
        // If the view doesn't exist, $viewFolder is ignored
        $this->viewParams['folder'] = '';

        // Call all setup hooks related to content type.
        Event::run('bluebox.createtemplate.' .CONTENT_TYPE, $this);

        /********************
        * PREPARE TEMPLATE *
        ********************/
        $this->template = $this->viewParams['folder'] . $this->viewParams['template'];
        
        // Call our parent controller's constructor
        // NOTE: This prepares our view and converts $this->template into an object.
        // Changes to $this->viewParams['template'] are ignored past this point
        parent::__construct();

        /*******************
        * PREPARE CONTENT *
        *******************/
        if (CONTENT_TYPE == 'html')
        {
            // Initialize default HTML content
            $this->template->css = '';

            $this->template->js = '';

            $this->template->meta = '';

            $this->template->header = '';

            $this->template->footer = '';
            
            // Set the page title
            $this->template->title = ucfirst(Router::$controller);

            if (Router::$method != 'index')
            {
                // Index is always a boring name, don't use it (but use everything else)
                $this->template->title .= ' -> ' . ucfirst(Router::$method);
            }
        }

        try
        {
            $this->template->content = new View($this->viewParams['folder'] . $this->viewParams['name']);
        } 
        catch(Exception $e)
        {
            try
            {
                $this->template->content = new View($this->viewParams['name']);
            } 
            catch(Exception $e)
            {
                $this->template->content = new View();

                $this->template->body = $this->viewParams['fallback'];
            }
        }

        /********************
        * FINALIZE CONTENT *
        ********************/
        // Make it easier to get to and remember how to access the core view by setting up a reference to the template, called view
        $this->view = & $this->template->content;

        // Call the constructor's for all plugins registered
        plugins::construct();

        // Setup anything related to authorizing the user
        Event::run('bluebox.ready', $this);
    }

    /**
     * This generic create function will add new entries of $baseModel
     */
    public function create()
    {
        $base = strtolower($this->baseModel);
        
        $this->createView();

        $this->loadBaseModel();

        $this->updateOnSubmit($this->$base);

        $this->prepareUpdateView();
    }

    /**
     * This generic edit function will update entries of $baseModel
     */
    public function edit($id = NULL)
    {
        $base = strtolower($this->baseModel);

        $this->createView();

        $this->loadBaseModel($id);

        $this->updateOnSubmit($this->$base);
        
        $this->prepareUpdateView();
    }

    /**
     * This generic delete function will remove entries of $baseModel
     */
    public function delete($id = NULL)
    {
        $base = strtolower($this->baseModel);

        $this->createView();

        $this->loadBaseModel($id);

        $this->deleteOnSubmit($this->$base);

        $this->prepareDeleteView(NULL, $id);
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

    public function getAuthBypass()
    {
        return $this->authBypass;
    }

    /**
     * Checks for the existance of speciall vars in the post to determine
     * if the page has been submitted and if so weither the action was cancle or
     * confirm.
     *
     * @param array A list of the parameters to controll the vars it checks for
     * @return mixed
     */
    public function submitted($options = array())
    {
        // if options is not provided as an array assume it is a
        // requestVar
        if (!is_array($options))
        {
            $options = array('requestVar' => $options);
        }

        // load the defaults into the options array
        $options += array (
            'confirmKey' => self::SUBMIT_CONFIRM,
            'denyKey' => self::SUBMIT_DENY,
            'requestVar' => 'submit'
        );

        if (!empty($_REQUEST[$options['requestVar']][$options['denyKey']]))
        {
            return self::SUBMIT_DENY;
        }
        else if (!empty($_REQUEST[$options['requestVar']][$options['confirmKey']]))
        {
            return self::SUBMIT_CONFIRM;
        }
        else if (sizeof($this->input->post()) != 0)
        {
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
        if ($this->auto_render == TRUE)
        {        
            // If XML, render it
            if (CONTENT_TYPE == 'xml')
            {
                echo $this->renderXml();
            } 
            elseif (CONTENT_TYPE == 'json')
            {
                // Render the template when the class is destroyed
                //$output = $this->template->render(false);
                echo $this->renderJson();
            } 
            else
            {
                if (!empty($this->template->title))
                {
                    $this->template->title = __($this->template->title);
                }
                
                if (!empty($this->view->title))
                {
                    $this->view->title = __($this->view->title);
                }

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
                
                $this->post_template($assets);

                $_SESSION['session.test'] = 'test';

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

    /**
     * This function will close a qtipAjaxForm with a message and action
     *
     * @param Doctrine_Record The newly created doctrine record
     * @param bool If true this it will force the reply even if no qtipAjaxForm is present
     */
    public function returnQtipAjaxForm($object = NULL, $force = FALSE)
    {
        if (!empty($_REQUEST['qtipAjaxForm']) || $force)
        {
            if (headers_sent())
            {
                kohana::log('error', 'Unable to reply to modal because headers are already sent');

                die();
            }

            $this->template->content = new View('generic/blank');

            header("X-AjaxForm-Status: complete");

            if(method_exists($this, 'qtipAjaxReturn'))
            {
                $this->qtipAjaxReturn($object);
            }

            message::render(array(), array('growl' => TRUE, 'html' => FALSE));

            $this->_render();

            flush();

            die();
        }
    }

    /**
     * This will inform a qtipAjaxForm request that there is not more
     * context, and therefore it will hide.  Usefull for 'success' actions
     *
     * @param bool If true this it will force the reply even if no qtipAjaxForm is present
     */
    public function exitQtipAjaxForm($force = FALSE)
    {
        if (!empty($_REQUEST['qtipAjaxForm']) || $force)
        {
            if (headers_sent())
            {
                kohana::log('error', 'Unable to reply to modal because headers are already sent');

                die();
            }

            header('HTTP/1.0 204 No Content');

            header("X-AjaxForm-Status: cancel");

            header('Content-Length: 0',true);

            header('Content-Type: text/html',true);

            flush();

            die();
        }
    }

    public function qtipAjaxReturn($data)
    {
        javascript::codeBlock('$(\'.jqgrid_instance\').trigger("reloadGrid");');
    }
    
    /**
     * Performs de-population of model :)
     *
     * @param object The base object to save
     * @param string The message to display if successful
     * @return bool
     */
    protected function formDelete(&$object, $deleteMessage = NULL, $deleteEvents = array())
    {
        // Delete data and all relations
        try
        {
            // Bring out the Gimp.
            $conn = Doctrine_Manager::connection();

            $conn->beginTransaction();

            $this->delete_prepare($object);

            // Allow plugins to process any data related to this object prior to deletion
            if(!plugins::delete($this, $deleteEvents))
            {
                throw new Bluebox_Exception('Plugins failed to delete');
            }

            $this->pre_delete($object);

            // Delete this base record
            $object->delete();

            $conn->commit();

            $this->post_delete($object);

            // Success - optionally set a delete message
            if (is_null($deleteMessage))
            {
                $displayName = inflector::humanizeModelName(get_class($object));

                message::set($displayName . ' removed!', array(
                    'type' => 'success'
                ));
            } 
            else if (!empty($deleteMessage))
            {
                message::set($deleteMessage, array(
                    'type' => 'success'
                ));
            }

            $this->delete_succeeded($object);

            return TRUE;
        } 
        catch(Doctrine_Connection_Exception $e)
        {
            message::set('Doctrine error: ' . $e->getMessage());
        } 
        catch(Bluebox_Exception $e)
        {
            message::set('Please correct the errors listed below.');

            kohana::log('alert', $e->getMessage());
        } 
        catch (Exception $e)
        {
            message::set($e->getMessage());
        }

        if ($conn)
        {
            $conn->rollback();
        }

        $this->delete_failed($object);

        return FALSE;
    }

    /**
     * Performs population of model
     *
     * @param object The base object to save
     * @param string The message to display if successful
     * @return bool
     */
    protected function formSave(&$object, $saveMessage = NULL, $saveEvents = array())
    {
        // Determine name of the base model for the object being saved
        if (get_parent_class($object) == 'Bluebox_Record')
        {
            $baseClass = get_class($object);
        } 
        else
        {
            $baseClass = get_parent_class($object);
        }

        // Import any post vars with the key of this model into the object
        $formData = $this->input->post(strtolower($baseClass), array());

        $object->fromArray($formData);

        // Save data and all relations
        try
        {
            $this->save_prepare($object);

            // Allow plugins to process any form-related data we just got back and attach to our data object
            if(!plugins::save($this, $saveEvents))
            {
                throw new Bluebox_Exception('Plugins failed to save');
            }

            $this->pre_save($object);

            // Save this base record
            $object->save();

            $this->post_save($object);

            // Success - optionally set a save message
            if (is_null($saveMessage))
            {
                $displayName = inflector::humanizeModelName(get_class($object));

                message::set($displayName .' saved!', array(
                    'type' => 'success'
                ));
            } 
            else if (!empty($saveMessage))
            {
                message::set($saveMessage, array(
                    'type' => 'success'
                ));
            }

            $this->save_succeeded($object);

            return TRUE;
            
        } 
        catch(Doctrine_Connection_Exception $e)
        {
            message::set('Doctrine error: ' . $e->getMessage());
        } 
        catch (Bluebox_Validation_Exception $e)
        {
            message::set('Please correct the errors listed below.');
            
            kohana::log('alert', $e->getMessage());
        } 
        catch(Bluebox_Exception $e)
        {
            message::set('Please correct the errors listed below.');

            kohana::log('alert', $e->getMessage());
        } 
        catch (Exception $e)
        {
            message::set($e->getMessage());   
        }

        $this->save_failed($object);

        return FALSE;
    }

    protected function updateOnSubmit($base)
    {
        if ($action = $this->submitted())
        {
            Event::run('bluebox.updateOnSubmit', $action);

            if (($action == self::SUBMIT_CONFIRM) AND ($this->formSave($base)))
            {
                $this->returnQtipAjaxForm($base);

                url::redirect(Router_Core::$controller);
            } 
            else if ($action == self::SUBMIT_DENY)
            {
                $this->exitQtipAjaxForm();

                url::redirect(Router_Core::$controller);
            }
        }
    }

    protected function deleteOnSubmit($base)
    {
        if ($action = $this->submitted(array('submitString' => 'delete')))
        {
            Event::run('bluebox.deleteOnSubmit', $action);

            if (($action == self::SUBMIT_CONFIRM) AND ($this->formDelete($base)))
            {
                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router_Core::$controller);   
            } 
            else if ($action == self::SUBMIT_DENY)
            {    
                $this->exitQtipAjaxForm();

                url::redirect(Router_Core::$controller);
            }
        }
    }

    protected function createView($baseModel = NULL, $forceDelete = NULL)
    {
        // Overload the update view
        if (($forceDelete) or (strcasecmp(Router::$method, 'delete') == 0 and $forceDelete !== FALSE))
        {
            $this->template->content = new View('generic/delete');
        }
        else
        {
            $this->template->content = new View(Router::$controller . '/update');
        }

        if (is_null($baseModel))
        {
            $baseModel = ucfirst($this->baseModel);
        }

        $this->view->title = ucfirst(Router::$method) .' ' .inflector::humanizeModelName($baseModel);

        Event::run('bluebox.create_view', $this->view);
    }

    protected function loadBaseModel($id = NULL, $baseModel = NULL)
    {
        if (is_null($baseModel))
        {
            $baseModel = $this->baseModel;
        }

        // Short hand for the baseModel
        $base = strtolower($baseModel);

        if (is_null($id))
        {
            $this->$base = new $baseModel();
        }
        else
        {
            $this->$base = Doctrine::getTable($baseModel)->find($id);

            // Was anything retrieved? If no, this may be an invalid request
            if (!$this->$base)
            {
                // Send any errors back to the index
                message::set('Unable to locate ' .ucfirst($baseModel) .' id ' .$id .'!');

                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->set_global('base', $base);

        Event::run('bluebox.load_base_model', $this->$base);
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        if (is_null($baseModel))
        {
            $baseModel = $this->baseModel;
        }
        
        $base = strtolower($baseModel);

        // Allow our location object to be seen by the view
        $this->view->set_global($base, $this->$base);

        Event::run('bluebox.prepare_update_view', $this->view);

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    protected function prepareDeleteView($baseModel = NULL, $id = 0)
    {
        if (is_null($baseModel))
        {
            $baseModel = $this->baseModel;
        }

        $base = strtolower($baseModel);

        $this->view->set_global($base, $this->$base);

        // Set the vars that the generic delete will be expecting
        $this->view->baseModel = strtolower($baseModel);

        if (isset($this->{$base}['name']))
        {
            $this->view->name = '\'' . $this->{$base}['name'] .'\'';
        }
        else if (isset($this->{$base}['number']))
        {
            $this->view->name = '\'' . $this->{$base}['number'] .'\'';
        }
        else
        {
            $this->view->name = ' id ' .$id;
        }

        Event::run('bluebox.prepare_delete_view', $this->view);

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    protected function post_template(&$assets)
    {
        // Run an event calling jquery helper and updating the $jqueryAssets with assets
        Event::run('system.post_template', $assets);

        /**
         * TODO: This is part of the silliest hack I have ever had to do, but
         * setting this session var in the event during an ajax call segfaults
         * 0xb7b6474a in gc_remove_zval_from_buffer () from /etc/httpd/modules/libphp5.so
         */
        $session = Session::instance();

        $session->set('javascript.onPageAssets', self::$onPageAssets['js']);

        $session->set('stylesheet.onPageAssets', self::$onPageAssets['css']);
    }

    protected function delete_prepare(&$object)
    {
        // Let things know we are about to delete
        Event::run('bluebox.delete_prepare', $object);
    }

    protected function pre_delete(&$object)
    {
        // Let things mess with our object
        Event::run('bluebox.pre_delete', $object);
    }

    protected function post_delete(&$object)
    {
        // Let things respond to the delete
        Event::run('bluebox.post_delete', $object);
    }

    protected function delete_succeeded(&$object)
    {
        // Let things respond to a failed save
        Event::run('bluebox.delete_succeeded', $object);
    }

    protected function delete_failed(&$object)
    {
        // Let things respond to a failed save
        Event::run('bluebox.delete_failed', $object);
    }

    protected function save_prepare(&$object)
    {
        // Let things know we are about to save
        Event::run('bluebox.save_prepare', $object);
    }

    protected function pre_save(&$object)
    {
        // Let things mess with our object
        Event::run('bluebox.pre_save', $object);
    }

    protected function post_save(&$object)
    {
        // Let things respond to the save
        Event::run('bluebox.post_save', $object);
    }

    protected function save_succeeded(&$object)
    {
        // Let things respond to a failed save
        Event::run('bluebox.save_succeeded', $object);
    }

    protected function save_failed(&$object)
    {
        // Let things respond to a failed save
        Event::run('bluebox.save_failed', $object);
    }
}