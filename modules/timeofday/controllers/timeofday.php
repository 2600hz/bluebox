<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDay_Controller extends FreePbx_Controller
{
    protected $baseModel = 'TimeOfDay';
    protected $writable = array(
        'name',
        'time',
        'mon',
        'tue',
        'wen',
        'thur',
        'fri',
        'sat',
        'sun',
        'during_number_id',
        'outside_number_id'
    );

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('jslider', 50);
        javascript::add(array('jquery.dependClass.js', 'jquery.slider.js'), 50);
    }
    /**
     * Method for the main page of this module
     */
    public function index()
    {
        $this->template->content = new View('generic/grid');

        $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Routes List'
                ))
            ->add('time_of_day_id', 'ID', array('hidden' => true, 'key' => true))
            ->add('name', 'Route Name')
            ->navButtonAdd('Columns', array(
                'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }' ,
                'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png' ,
                'title' => 'Show/Hide Columns' ,
                'noCaption' => true ,
                'position' => 'first'
                ))
            ->addAction('timeofday/edit', 'Edit', array(
                'arguments' => 'time_of_day_id' ,
                'width' => '200'
                ))
            ->addAction('timeofday/delete', 'Delete', array(
                'arguments' => 'time_of_day_id',
                'width' => '200'
                )
            );

        // Allow plugins to add to the grid, too
        plugins::views($this);

        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    /**
     * Method to add an item
     */
    public function add()
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' a Time Based Route';

        $this->timeOfDay = new $this->baseModel();
        if ($this->submitted()) {
            if ($this->formSave($this->timeOfDay)) {
                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->timeofday = $this->timeOfDay;
        plugins::views($this);
    }

    /**
     * Method to edit an item
     * @param $id integer
     */
    public function edit($id = NULL)
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' a Time Based Route';

        $this->timeOfDay = Doctrine::getTable($this->baseModel)->find($id);
        if (! $this->timeOfDay) {
            Kohana::show_404();
        }

        if ($this->submitted()) {
            if ($this->formSave($this->timeOfDay)) {
                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->timeofday = $this->timeOfDay;
        plugins::views($this);
    }

    /**
     * Method to delete an item
     * @param $id integer
     */
    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }

    public function qtipAjaxReturn($data) {
        javascript::codeBlock('$(\'.jqgrid_instance\').trigger("reloadGrid");');
    }
}