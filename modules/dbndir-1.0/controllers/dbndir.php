<?php defined('SYSPATH') or die('No direct access allowed.');

class dbndir_Controller extends Bluebox_Controller {
	
    protected $baseModel = 'Dbndir';
	
	public function index()
	{
        $this->template->content = new View('generic/grid');
        $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Directories'
            )
        );
        // Add the base model columns to the grid
        $this->grid->add('dbn_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $this->grid->add('dbn_name', 'Directory Name');
        $this->grid->addAction('dbndir/edit', 'Edit', array(
                'arguments' => 'dbn_id'
            )
        );
//        $this->grid->addAction('callcenter_agents/delete', 'Delete', array(
//                'arguments' => 'cca_id'
//            )
//        );
        plugins::views($this);
        $this->view->grid = $this->grid->produce();
	}
	
	
}
?>