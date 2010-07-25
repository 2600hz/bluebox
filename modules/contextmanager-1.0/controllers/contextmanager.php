<?php defined('SYSPATH') or die('No direct access allowed.');

class ContextManager_Controller extends Bluebox_Controller
{
    public $baseModel = 'Context';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Contexts'
            )
        );

        // Add the base model columns to the grid
        $grid->add('context_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Context Name');

        // Add the actions to the grid
        $grid->addAction('contextmanager/edit', 'Edit', array(
                'arguments' => 'context_id',
            )
        );
        $grid->addAction('contextmanager/rebuild', 'Rebuild', array(
                'arguments' => 'context_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('contextmanager/delete', 'Delete', array(
                'arguments' => 'context_id',
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function rebuild($context_id)
    {
        $this->loadBaseModel($context_id);

        foreach($this->context['NumberContext'] as $key => &$numberContext)
        {
            $number = &$numberContext['Number'];

            $number->markModified('number');
        }

        try
        {
            $this->context->save();

            message::set('Context ' .$this->context['name'] .' dialplan rebuild complete!', 'success');
        }
        catch (Exception $e)
        {
            message::set($e->getMessage());
        }

        $this->returnQtipAjaxForm();

        url::redirect(Router_Core::$controller);
    }
}