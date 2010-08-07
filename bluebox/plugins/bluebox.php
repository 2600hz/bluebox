<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Bluebox/Plugins
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
abstract class Bluebox_Plugin
{
    protected $name = NULL;

    protected $subview = NULL;

    protected $base = NULL;

    protected $formData = array();

    protected $pluginData = array();

    protected $preloadModels = NULL;

    /**
     * The name of the base model that this class is likely to reference. For example, if you are making an address plugin module,
     * your base model is probably 'Address'. Must be capitalized in Doctrine form and match an existing model.
     *
     * @var string
     */
    
    protected $baseModel;
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

    public function __construct()
    {
        // Take a guess at the core model name, and initialize that model. This is important! It will build relationships properly
        // prior to anything being loaded in a core application page while preventing initialization of models that are unlikely to be used.
        //
        // TODO: Change this to initialize all models within this plugin's models/ directory
        if (is_array($this->preloadModels)) foreach ($this->preloadModels as $modelName)
        {
            Doctrine::initializeModels($modelName);
        }
        
        $modelName = str_replace('_Plugin', '', get_class($this));

        Doctrine::initializeModels($modelName);

    }

  /**
   * Try to get properties that didn't exist in the plugin class from the event system, if we were called from an event
   * @param string $name Name of variable to try to get
   * @return <type>
   */
    public function &__get($name)
    {
        $class = Event::$data;

        # we have an event
        if(is_object ($class))
        {
            if(property_exists($class, $name))
            {
                return $class->$name;
            }
        }

        # property did not exist in class or event..
        throw new Exception( " Property " . $name . " does not exist in the class " . get_class( $class ) . "." );
    }

  /**
   * See if property exists in the plugin class from the event system, if we were called from an event
   * @param string $name Name of variable to test
   * @return boolean TRUE or FALSE
   */
    public function  __isset($name)
    {
        $class = Event::$data;

        # we have an event
        if(is_object ($class))
        {
            //if (property_exists($class, $name)) {
            if (isset($class->$name))
            {
                return TRUE;
            }
        }

        # property did not exist in class or event..
        return FALSE;
    }

  /**
   * Set properties in the main application if they are defined there but not in the local plugin class
   * @param string $name Name of variable to try to get
   * @param object $value Value to set (can be any variable type)
   * @return <type>
   */
    public function __set($name, $value)
    {
        $class = Event::$data;

        # this gets called for every unknown.
        # lets make sure we are from an event
        if(is_object ($class))
        {
            if (property_exists($class, $name))
            {
                $class->$name = $value;
            }
        }
    }

  /**
   * Catch function calls that did not exist in plugin class, see if they exist in the main application
   * @param string $name Name of variable to try to get
   * @param object $arguments Value to pass as arguments (can be any variable type, or array, etc.)
   * @return <type>
   */
    public function __call($name, $arguments)
    {
        $class = Event::$data;

        # we have an event
        if(is_object ($class))
        {
            # call the function if it exists in the event class
            if(method_exists($class, $name))
            {
                return call_user_func_array(array($class, $name), $arguments);
            }

        }

        # if it didnt exist in the main class or the Event class then throw an error
        throw new Exception( " Method " . $name . " not exist in this class " . get_class( $class ) . "." );
    }

  /**
   * Helper function for plugins. If a plugin is instantiated as a singleton, we automatically provide a getInstance() method.
   * Set properties in the main application if they are defined there but not in the local plugin class
   * @return object Instance of self
   */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $class = __CLASS__;

            self::$instance = new $class;
        }

        return self::$instance;
    }

    /**
     * Based on the $baseModel variable, returns the root model from the main controller, if available
     * This assumes the controller sets a variable named after $baseModel (camelcased). So if $baseModel = 'Device' then
     * it is presumed that $this->device has been set with a model object of type Device()
     *
     * @return Bluebox_Record Returns a Doctrine/Bluebox record referring to the base model object
     */
    public function getBaseModelObject()
    {
        // Can't always assume we're a controller here...
        if (method_exists(Event::$data, 'getBaseModel'))
        {
            $baseObjectName = $this->getBaseModel();

            //$baseObjectName[0] = strtolower($baseObjectName[0]);
            $baseObjectName = strtolower($baseObjectName);
        } 
        else
        {
            return NULL;
        }

        if ($this->__isset($baseObjectName))
        {
            $model =& $this->__get($baseObjectName);

            // Only return real database objects, otherwise assume NULL. For safety.
            if (!($model instanceof Doctrine_Record))
            {
                $model = NULL;
            }

        } 
        else
        {
            $model = NULL;
        }

        return $model;
    }
    
    public function save()
    {
        if (!$this->saveSetup())
        {
            return TRUE;
        }

        if (!$this->loadFormData())
        {
            return TRUE;
        }

        if (!$this->addPluginData())
        {
            return FALSE;
        }

        return TRUE;
    }

    public function update()
    {
        if (!$this->viewSetup())
        {
            return FALSE;
        }

        if (!$this->loadViewData())
        {
            return FALSE;
        }

        if (!$this->addSubView())
        {
            return FALSE;
        }

        return TRUE;
    }

    protected function viewSetup()
    {
        // Determine the name of this plugin
        if (empty($this->name))
        {
            kohana::log('error', 'Plugin name unknown, ignoring');

            return FALSE;
        }
        
        $this->subview = new View($this->name .'/update');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }

    protected function loadViewData()
    {
        $this->base = $this->getBaseModelObject();

        if (!isset($this->base['plugins']))
        {
            return FALSE;
        }

        if (isset($this->base['plugins'][$this->name]))
        {
            $this->subview->{$this->name} = $this->base['plugins'][$this->name];
        }

        return TRUE;
    }

    protected function addSubView()
    {
        $this->views[] = $this->subview;

        return TRUE;
    }

    protected function saveSetup()
    {
        // Determine the name of this plugin
        if (empty($this->name))
        {
            kohana::log('error', 'Plugin name unknown, ignoring');

            return FALSE;
        }

        // Get the base object
        $this->base = $this->getBaseModelObject();

        kohana::log('debug', 'Attempting to attach plugin data ' .$this->name .' to ' .get_class($this->base));

        // If the base object doesnt have a place for plugins we are done
        if (!isset($this->base['plugins']))
        {
            kohana::log('error', 'Base does not contain the plugins column');

            return FALSE;
        }

        return TRUE;
    }

    protected function loadFormData()
    {
        // Get any data coming from the form for this plugin
        $this->formData = $this->input->post($this->name, array());

        // If the plugin already has data merge what came from the form
        if (isset($this->base['plugins'][$this->name]))
        {
            $this->pluginData = arr::merge($this->base['plugins'][$this->name], $this->formData);
        }
        else
        {
            $this->pluginData = $this->formData;
        }

        return TRUE;
    }

    protected function addPluginData()
    {
        $validator = Bluebox_Controller::$validation;

        $errorCount = count($validator->errors());
        
        // Remove any empty keys, no need to store them
        $this->pluginData = array_filter($this->pluginData);

        // Destroy the existing plugin key for this plugin using the new data
        $this->base['plugins'] = array_merge(
            (array)$this->base['plugins'],
            array($this->name => $this->pluginData)
        );

        if($this->validate($this->pluginData, $validator) === FALSE OR $errorCount != count($validator->errors()))
        {
            return FALSE;
        }
        
        return TRUE;
    }

    protected function validate($data, $validator)
    {
        return TRUE;
    }
}