<?php
/**
 * This is the skeleton controller template.
 * Replace this text with a description of what your module or pages do.
 *
 * All public methods here that are not prefixed with _ are accessible via /MyModule/methodname (where "MyModule" is the name of the
 * controller class and "methodname" is the name of the method you define).
 * As an example, if this class is named Voicemail_Controller with a method definition of 'public function send()', you can access it via:
 *      http://myserver/frepbx/voicemail/send
 *
 * You can add your own custom routes by adding the appropriate routing file per Kohana's specifications.
 *
 * Views are automatically rendered for each method unless you override this functionality. The view file rendered, by default, is
 *  /views/controllername/methodname
 * Using the above example, the view for http://myserver/bluebox/voicemail/send would live in
 *  /views/voicemail/send
 *
 * To set variables that will be accessible in the view, use the format:
 *  $this->view->myvariable = $myvariable;
 * You can then reference those variables as $myvariable within the view itself.
 *
 *
 * @author Darren Schreiber
 * @package _Skeleton
 */
class MyModule_Controller extends Bluebox_Controller {
    /**
     * You can override the system/user's default skin on a per-controller basis if you need to. Note that this is the highest
     * level override you can do - it will always override any user or system defaults.
     * @var string Skin name (as it exists in /skins/)
     */
    protected $skin;

    /**
     * By default, we automatically render the page. You can override this controller-wide, or within individual methods
     * @var boolean true/false as to whether we should be automatically rendering a view after the method finishes
     */
    public $auto_render = true;

    /**
     * By default, all public fields defined in this class are also available as JSON/XML as well. You can hide some fields from
     * being presented to requestors in XML/JSON format if you don't like this behavior. Setting this value to '*' hides all data fields.
     * @var array
     */
    public $hidden_fields = NULL;

    /**
     * An array of methods/actions that are accessible without the user needing to be authenticated
     * @var array
     */
	public $noAuth = NULL;

    /**
     * Keep track of any grid helpers when used, as it relates to the main page. We typically only have one.
     * Note that this variable is exposed to plug-ins, so that they can add additional information to grids.
     * If you use your own variable name, there's no guarantees plug-ins will be able to use it.
     * @var array Array of Grid_Helper objects
     */
    public $grids;

    /**
     * The order columns should appear. This is set in a simple array and is used by the grid to re-order columns.
     * This is useful for allowing individual users to have their own custom column ordering.
     * @var array
     */
    public $columnOrder;

    /**
     * Array of key/value pairs to filter on.
     * @var array
     */
    public $filters;

    /**
     * Base index page
     */
    public function index()
    {
        // You could read database information here
        $widgets = Doctrine::getTable('Widget')->findByFieldname('Widget');

        // Pass the widgets you retrieved to the view. You can then retrieve individual fields in the view as $widgets->field_name.
        $this->view->widgets = $widgets;

        // The view automatically renders as MyModule/index (based on controller name and function name)
        // The view is stored in views/MyModule/index

        // If you don't want the view to auto render, uncomment this line
        //$this->auto_render = false;
    }

    /**
     * Add page
     */
    public function add()
    {
        // Create a new database object
        $newWidget = new Widget();

        // Pass the new widget to the view, just for reference
        $this->view->widget = $newWidget;

        // NOTE: If you do anything fancier then what's above, or if you want this page to also be accessible via XML/RPC, SOAP, etc.
        // you should move your logic to a library and instantiate + call that library from here.
    }

    /**
     * Edit page
     * @param integer $widgetId
     */
    public function edit($widgetId)
    {
        // Retrieve an existing database object
        $widget = Doctrine::getTable('Widget')->find($widgetId);

        // Does widget exist? If not, show a 404 error page (to thwart hack attempts and confuse)
        if (!$widget) {
            Kohana::show_404();
        }

        // Pass the retrieved widget to the view so the fields can be displayed for editing
        $this->view->widget = $widget;
    }

    /**
     * Save page
     */
    public function save()
    {
        $widgetId = $this->input->get('mymodule.widgetId');

        // Retrieve an existing "widget", if a widgetId was specified. Otherwise we assume this is a new widget
        $this->widget = Doctrine::getTable('Widget')->find($widgetId);

        // No widget? Create a new one
        if (!$this->widget)
            $this->widget = new Widget();

        // Copy form fields into the widget
        $this->widget->field1 = $this->input->post('field1');
        $this->widget->field2 = $this->input->post('field2');

        // Allow modules attached to this page to process any form-related data we just got back
        // Modules will have access to the widget object as $this->widget, and can add, modify or delete data in that object or any related object tables
        // NOTE: This does not execute a save, it only gives the opportunity to modules to add data to the object.
        $this->moduleForms();

        try {
            // Try to save. We will get a validation error from Doctrine if any form fields are invalid, or a connection error if the
            // database is not accessible.
            $this->widget->save();
        } catch (Doctrine_Connection_Exception $e) {
            $this->view->message = 'Database error: ' . $e->getPortableMessage();
        } catch (Doctrine_Validation_Exception $e) {
            $this->view->message = 'Please correct these forms errors: ' . $e->getPortableMessage();
        }

        url::redirect('/mymodule/edit/' . $widgetId);   // After saves, go back to the edit page
    }

    /**
     * TODO: I need to write this example.
     */
    function delete()
    {

    }


}
