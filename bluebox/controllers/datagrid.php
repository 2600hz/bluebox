<?php defined('SYSPATH') or die('No direct access allowed.');

class DataGrid_Controller extends Bluebox_Controller {

    public function  __construct() {
        parent::__construct();
        javascript::add('mustache');
    }

    public function index()
    {
        $data = array(
            array('key1' => 'Value 0-1', 'key2' => 'Value 0-2', 'key3' => 'Value 0-3'),
            array('key3' => 'Value 1-3', 'key2' => 'Value 1-2', 'key1' => 'Value 1-1'),
            array('key1' => 'Value 2-1', 'key3' => 'Value 2-3'),
            array(),
        );

        Benchmark::start('grid');
        
        $grid = DataGrid_Factory::create('test');

        $grid->table->border = 1;

        $grid->table->head->row()->headerCell('This is a test');
        
        $grid->addField('key1', 'Header 1');
        $grid->addField('key3', 'Header 3');
        $grid->addField('key2', 'Header 2');

        $grid->table->foot->row()->headerCell('End of data')->style = 'text-align:center;';

        $this->view->table = $grid->render('table', $data);

        Benchmark::stop('grid');
        $benchmark = Benchmark::get('grid');

        $this->view->time = $benchmark['time'];
    }

    public function simpleData()
    {
        $this->template->content = new View('datagrid/index');
        
        $row = array();
        for($column = 1; $column <= 20; $column++) {
            $row['key' .$column] = 'Data ' .$column;
        }

        $data = array();
        for($rows = 1; $rows <= 100; $rows++) {
            $data[] = $row;
        }

        Benchmark::start('grid');

        $grid = DataGrid_Factory::create('test');

        $grid->table->border = 1;

        for($column = 1; $column <= count($data[0]); $column++) {
            $grid->addField('key' .$column, 'Header ' .$column);
        }

        $this->view->table = $grid->render('table', $data);

        Benchmark::stop('grid');
        $benchmark = Benchmark::get('grid');

        $this->view->time = $benchmark['time'];
    }

    public function tableFromTemplate()
    {
        $this->template->content = new View('datagrid/index');

        $data = $this->templateData();

        Benchmark::start('grid');

        $grid = DataGrid_Factory::create('test');

        $grid->table->border = 1;

        for($column = 1; $column <= count($data['head']); $column++) {
            $grid->addColumn('{{key' .$column .'}}', 'Header ' .$column);
        }

        $this->view->table = $grid->render('table', $data);

        Benchmark::stop('grid');
        $benchmark = Benchmark::get('grid');

        $this->view->time = $benchmark['time'];
    }

    public function skeleton()
    {
        $data = $this->templateData();

        Benchmark::start('grid');
        
        $grid = DataGrid_Factory::create('test');
        
        $grid->table->border = 1;

        for($column = 1; $column <= count($data['head']); $column++) {
            $grid->addField('key' .$column, 'Header ' .$column);
        }
        
        $this->view->skeleton = $grid->render('skeleton');

        Benchmark::stop('grid');
        $benchmark = Benchmark::get('grid');

        $this->view->time = $benchmark['time'];
    }

    public function template()
    {
        $data = $this->templateData();

        Benchmark::start('grid');

        $grid = DataGrid_Factory::create('test');

        $grid->table->border = 1;

        for($column = 1; $column <= count($data['head']); $column++) {
            $grid->addField('key' .$column, 'Header ' .$column);
        }

        $this->view->template = $grid->render('template');

        Benchmark::stop('grid');
        $benchmark = Benchmark::get('grid');
        
        $this->view->time = $benchmark['time'];
        $this->view->data = json_encode($this->templateData());
    }

    public function partials()
    {
        $data = $this->templateData();

        Benchmark::start('grid');
        
        $grid = DataGrid_Factory::create('test');

        $grid->table->border = 1;

        for($column = 1; $column <= count($data['head']); $column++) {
            $grid->addField('key' .$column, 'Header ' .$column);
        }

        $grid->addColumn('static data', 'Static Head');

        $template = $grid->render('partials');

        $this->view->template = $template['template'];

        Benchmark::stop('grid');
        $benchmark = Benchmark::get('grid');

        $this->view->time = $benchmark['time'];

        $this->view->partials = json_encode($template['partials']);
        $this->view->data = json_encode($this->templateData());
    }
    
    protected function templateData() {
        $head = $row = $foot = array();
        for($column = 1; $column <= 10; $column++) {
            $head['key' .$column] = 'Header ' .$column;
            $row['key' .$column] = 'Data ' .$column;
            $foot['key' .$column] = 'Footer ' .$column;
        }

        $body = array();
        for($rows = 1; $rows <= 100; $rows++) {
            $body[] = $row;
        }

        return array('head' => $head, 'body' => $body, 'foot' => $foot);
    }
}
