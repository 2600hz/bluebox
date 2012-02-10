<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * FeatureCode_Controller.php - FeatureCode Controller
 *
 * Allows you to specify arbitrary XML as a "feature code" and then assign/map those codes to destinations/numbers
 *
 * @author Darren Schreiber
 * @license MPL
 * @package Bluebox
 * @subpackage FeatureCode
 */
class CustomFeatureCode_Controller extends Bluebox_Controller
{
    protected $baseModel = 'CustomFeatureCode';

    public function  __construct()
    {
        parent::__construct();

        javascript::add('editarea/edit_area_full.js');
        javascript::add('featurecode.js');
        stylesheet::add('featurecode.css');
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Custom Feature Codes'
            )
        );

        // Add the base model columns to the grid
        $grid->add('custom_feature_code_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('description', 'Description');
        
        // Add the actions to the grid
        $grid->addAction('customfeaturecode/edit', 'Edit', array(
                'arguments' => 'custom_feature_code_id'
            )
        );
        $grid->addAction('customfeaturecode/delete', 'Delete', array(
                'arguments' => 'custom_feature_code_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
    public function post_save($object) {
	foreach (Doctrine::getTable('FeatureCode')->findBy('custom_feature_code_id',$object['custom_feature_code_id']) AS $featurecode) {
		print "feature code id = ".$featurecode['feature_code_id']."\n\n";
		foreach (Doctrine::getTable('Number')->findBy('foreign_id',$featurecode['feature_code_id']) AS $number) {
			if ($number['class_type']=='FeatureCodeNumber') {
				print "Number = ".$number['number']."\n\n";
				$number->markModified('number');
				$number->save();
			}
		}
	}
	parent::post_save($object); 
    }

   public function pre_save($object) {
	// Parsing invalid sprouts a warning, as well as an exception
	// we just want an exception.
	// Make sure error handler is restored, or else!
	// Also replace "String" with "Dialplan" in the error message

	try {
		set_error_handler(function ($errno,$errstr) {});
		$struct=new SimpleXMLElement("<extension>".$object['dialplan_code']."</extension>"); // Validate!
		restore_error_handler();
	} catch (Exception $e) {
		restore_error_handler();
		throw new Exception(str_replace('String','Dialplan',$e->getMessage()));
	}
   }
}
