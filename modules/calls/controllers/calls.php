<?php
/**
 * Description of calls
 *
 * @author Michael Phillips
 * @package FreePBX3
 * @subpackage Calls
 */
class Calls_Controller extends FreePbx_Controller {
	private $grid;
	
	public function __construct()
    {
        parent::__construct(); // This must be included
        /*
        $this->grid = new Grid();
		$this->grid->url = 'calls/grid';
		
		$this->grid->caption = 'Calls';
		$this->grid->add('CDR.caller_id_name', array('width' => 90) );		
		$this->grid->add('CDR.caller_id_number', array('width' => 90) );	
		$this->grid->add('CDR.destination_number', array('width' => 90) );
		$this->grid->add('CDR.duration', array('width' => 100, 'formatter' => 'integer') );
		$this->grid->add('CDR.start_stamp', array('width' => 100, 'formatter' => 'date', 'formatoptions' => array('srcformat' => 'Y-m-d h:i:s', 'newformat' => 'm/d/Y h:i:s')));
		$this->grid->add('CDR.end_stamp', array('width' => 100, 'formatter' => 'date', 'formatoptions' => array('srcformat' => 'Y-m-d h:i:s', 'newformat' => 'm/d/Y h:i:s')));

		$this->grid->from('CDR');
		$this->grid->colNames = array('Name', 'Number', 'Destination', 'Duration (s)', 'Start', 'End');

		*/
    }
    
	public function index() {

		
		
		//$this->template->css .= $this->grid->printCSS();
		//$this->template->js .= $this->grid->printJS();
		//$this->template->content->gridJS = $this->grid->generate();
		//$this->template->content->grid = $this->grid->printGrid();
    }
	
	public function grid()
	{
		//$this->auto_render = false;
		//echo $this->grid->getGridJson();
	}
}
