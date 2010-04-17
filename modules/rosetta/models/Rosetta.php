<?php
class Rosetta extends FreePbx_Record
{
	public function setTableDefinition()
    {
		$this->hasColumn('rosetta_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('ref_string', 'string', 1024, array('notblank' => true));
		$this->hasColumn('trans_string', 'string', 1024, array('notblank' => true));
	}
	
	public function setUp()
	{
		
		$this->actAs('I18n', array('fields' => array('trans_string')));
	}
}	
