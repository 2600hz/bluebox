<?php defined('SYSPATH') or die('No direct access allowed.');

class ValetParkingLot extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('vpl_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('vpl_name', 'string', 75, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('vpl_desc', 'string', 200);
		$this->hasColumn('vpl_start', 'integer', 11, array('notnull' => true, 'unsigned' => true));
		$this->hasColumn('vpl_end', 'integer', 11, array('unsigned' => true, 'notnull' => true));
	}

	public function setUp()
	{
		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}

    public function contains($fieldName)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::contains('vpl_name');
    	else
    		return parent::contains($fieldName);
    }
	
	public function get($fieldName, $load = true)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::get('vpl_name', $load);
    	else
    		return parent::get($fieldName, $load);
    }

}

?>