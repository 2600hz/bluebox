<?php defined('SYSPATH') or die('No direct access allowed.');
class Fax_Plugin extends Bluebox_Plugin
{
    protected $baseModel = 'FaxProfile';
    protected $name = 'fax';
    
    public static function provideNumberTargets()
	{
			$target_objects = array();

			$fxprs = Doctrine::getTable('FaxProfile')->findAll();

			foreach ($fxprs as $fxpr)
			{
				$target_objects[] = array(
						'object_name' => $fxpr->fxp_name,
						'object_description' => 'Fax',
						'object_number_type' => 'FaxProfileNumber',
						'object_id' =>  $fxpr->fxp_id,
				);
			}

			Event::$data['FaxProfileNumber'] = array(
				'short_name' => 'Fax',
				'display_name' => 'Fax',
				'target_objects' => $target_objects,
				'quick_add' =>'/fax/create'
			);
	}
	
	protected function viewSetup()
    {
        $this->subview = new View('fax/autofax');
        $this->subview->tab = 'main';
        $this->subview->section = 'general';
        return TRUE;
    }
	
}