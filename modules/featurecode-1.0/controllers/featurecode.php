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
        $grid->add('sections', 'Sections', array(
                'align' => 'center',
                'search' => FALSE,
                'sortable' => FALSE,
                'callback' => array(
                    'function' => array($this, '_showSections'),
                    'arguments' =>  array('registry')
                )
            )
        );
        
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

    public function _showSections($null, $registry)
    {
        if ( empty($registry) )
        {
            return 'None';
        }

        return array_sum(array_map('featurecode_defined_sections', array_keys($registry), array_values($registry))) . ' sections';
    }

    protected function pre_save(&$object)
    {
        $errors = array();
        
        foreach ( $object['registry'] as $section => $xml )
        {
            $dom = new DOMDocument('1.0');

            $dom->formatOutput = FALSE;

            if ( strlen(trim($xml)) == 0 )
            {
                kohana::log('debug', 'Section: ' . $section . ' Empty XML');

                $cleaned[$section] = '';

                continue;
            }

            try
            {
                if ( $dom->loadXML(trim($xml)) )
                {
                    $cleaned[$section] = $dom->saveXML();

                    kohana::log('debug', 'Section: ' . $section . ' Cleaned XML: ' . $dom->saveXML());

                    continue;
                }
                else
                {
                    $errors[] = ucfirst($section);
                }
            }
            catch(Exception $e)
            {
                $errors[] = ucfirst($section);
            }
        }

        if ( count($errors) > 0 )
        {
            throw new Exception ('Please correct the XML errors in these sections: ' . implode(', ', $errors));
        }

        $object['registry'] = $cleaned;

        kohana::log('debug', 'Successfully validated XML');

        parent::pre_save($object);
    }
}

function featurecode_defined_sections($section, $xml)
{
    return strlen($xml) > 0 ? 1 : 0;
}