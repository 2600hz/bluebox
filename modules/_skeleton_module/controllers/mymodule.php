<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * This is the skeleton controller template.
 * Replace this text with a description of what your module or pages do.
 *
 * All public methods here that are not prefixed with _ are accessible via the
 * url /MyModule/methodname (where "MyModule" is the name of the controller class
 * and "methodname" is the name of the method you define).
 *
 * As an example, if this class is named Voicemail_Controller with a method
 * definition of 'public function send()', you can access it via:
 *
 *      http://myserver/frepbx/voicemail/send
 *
 * You can add your own custom routes by adding the appropriate routing file
 * per Kohana's specifications.
 *
 * Views are automatically rendered for each method unless you override this
 * functionality. The view file rendered, by default, is
 *
 *  modules/mymodule/views/controllername/methodname
 *
 * Using the above example, the view for
 * http://myserver/bluebox/voicemail/send would live in
 *
 *  modules/voicemail/views/voicemail/send.php
 *
 * To set variables that will be accessible in the view, use the format:
 *
 *  $this->view->myvariable = $myvariable;
 *
 * You can then reference those variables as $myvariable within the view itself.
 */

/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class MyModule_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'MyModule';

    /**
     * Typically we create a grid, but you can define any entry point you
     * would like...
     */
    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'MyModule Grid Header'
            )
        );

        // Add the base model columns to the grid
        $grid->add('my_module_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('datafield1', 'Field 1');
        $grid->add('datafield2', 'Field 2');

        // Add the actions to the grid
        $grid->addAction('mymodule/edit', 'Edit', array(
                'arguments' => 'my_module_id'
            )
        );
        $grid->addAction('mymodule/delete', 'Delete', array(
                'arguments' => 'my_module_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;

        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    /**
     * Create, edit, and delete is all handled for you.  You can; however, involve
     * your contoller in the process where you need to deviate from the standard behaviour
     * or override it entirely.  You can accomplish this by either creating the
     * appropriate methods or hooking into the events.  Our you can do your own
     * thing with completely different method names, its up to you !
     */
}