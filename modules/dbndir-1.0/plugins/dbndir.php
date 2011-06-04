<?php defined('SYSPATH') or die('No direct access allowed.');
class Dbndir_Plugin extends Bluebox_Plugin
{
    protected $baseModel = 'dbndir';
    
    protected function viewSetup()
    {
        $this->subview = new View('dbndir/listingoptions');
        $this->subview->tab = 'main';
        $this->subview->section = 'general';
        return TRUE;
    }
    
	public static function provideNumberTargets()
	{
			$target_objects = array();

			$dirs = Doctrine::getTable('Dbndir')->findAll();

			foreach ($dirs as $dir)
			{
				$target_objects[] = array(
						'object_name' => $dir->dbn_name,
						'object_description' => 'Dial By Name Directory',
						'object_number_type' => 'DbndirNumber',
						'object_id' =>  $dir->dbn_id,
				);
			}

			Event::$data['DbndirNumber'] = array(
			'short_name' => 'Dbndir',
			'display_name' => 'Dial By Name Directory',
			'target_objects' => $target_objects,
			'quick_add' =>'/dbndir/create'
			);
	}
}