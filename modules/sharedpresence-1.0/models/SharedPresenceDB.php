<?php defined('SYSPATH') or die('No direct access allowed.');

class SharedPresenceDB extends Bluebox_Record
{	
   public static $errors = array (
        'spd_name' => array (
            'unique' => 'This name is already in use.'
        )
    );
	
	function setTableDefinition()
	{
		$this->hasColumn('spd_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('spd_name', 'string', 75, array('notnull' => true, 'notblank' => true, 'unique' => true));
		$this->hasColumn('spd_desc', 'string', 200);
	}

	public function setUp()
	{
		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('MultiTenant');
	}

    public function contains($fieldName)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::contains('spd_name');
    	else
    		return parent::contains($fieldName);
    }
	
	public function get($fieldName, $load = true)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::get('spd_name', $load);
    	else
    		return parent::get($fieldName, $load);
    }

}

?>