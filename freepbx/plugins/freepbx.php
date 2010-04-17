<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * freepbx.php - FreePbx plugin base class
 *
 * This class provides supporting functionality for FreePbx plug-ins. One of it's most important roles is allowing
 * a plug-in, when called from a FreePbx application, to have access to the same controller and other environment
 * variables via $this-> as are available within the controller itself, and to pass all changes back to the controller.
 *
 * This means that if a main application sets something via $this->somevar, it will be accessible as $this->somevar in a plugin.
 *
 * @author Karl Anderson
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

abstract class FreePbx_Plugin
{
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
        if (is_array($this->preloadModels)) foreach ($this->preloadModels as $modelName) {
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
            if(property_exists($class, $name)) {
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
            if (isset($class->$name)) {
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
            if (property_exists($class, $name)) {
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
            if(method_exists($class, $name)) {
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
     * @return FreePbx_Record Returns a Doctrine/FreePbx record referring to the base model object
     */
    public function getBaseModelObject() {
        // Can't always assume we're a controller here...
        if (method_exists(Event::$data, 'getBaseModel')) {
            $baseObjectName = $this->getBaseModel();
            $baseObjectName[0] = strtolower($baseObjectName[0]);
        } else {
            return NULL;
        }

        if ($this->__isset($baseObjectName)) {
            $model =& $this->__get($baseObjectName);

            // Only return real database objects, otherwise assume NULL. For safety.
            if (!($model instanceof Doctrine_Record)) {
                $model = NULL;
            }
        } else
            $model = NULL;

        return $model;
    }
}

