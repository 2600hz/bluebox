<?php defined('SYSPATH') or die('No direct access allowed.');

class PagingGroup extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('pgg_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('pgg_name', 'string', 75, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('pgg_type', 'string', 10, array('default' => 'page'));
		$this->hasColumn('pgg_desc', 'string', 200);
        $this->hasColumn('pgg_device_ids', 'array', 10000, array('default' => array()));
	}

	public function setUp()
	{
		$this->hasMany('PagingGroupNumber as Number', array('local' => 'pgg_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}

    public function contains($fieldName)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::contains('pgg_name');
    	else
    		return parent::contains($fieldName);
    }
	
	public function get($fieldName, $load = true)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::get('pgg_name', $load);
    	else
    		return parent::get($fieldName, $load);
    }

}

?>