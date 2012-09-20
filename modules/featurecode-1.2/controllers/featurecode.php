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
class FeatureCode_Controller extends Bluebox_Controller
{
    protected $baseModel = 'FeatureCode';

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
                'caption' => 'Feature Codes'
            )
        );

        // Add the base model columns to the grid
        $grid->add('feature_code_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('description', 'Description');
        
        // Add the actions to the grid
        $grid->addAction('featurecode/edit', 'Edit', array(
                'arguments' => 'feature_code_id'
            )
        );
        $grid->addAction('featurecode/delete', 'Delete', array(
                'arguments' => 'feature_code_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
    public function prepareUpdateView()
    {
	$featurecodes=array();
	foreach (Doctrine::getTable('CustomFeatureCode')->findAll(Doctrine::HYDRATE_ARRAY) AS $customfeature) {
		$featurecodes[$customfeature['custom_feature_code_id']]=$customfeature['name'];
	}
	$this->view->featurecodes=$featurecodes;
	return parent::prepareUpdateView();
    }

}
