<?php defined('SYSPATH') or die('No direct access allowed.');

class Dbndir extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('dbn_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('dbn_name', 'string', 25, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('dbn_desc', 'string', 1000);
		$this->hasColumn('dbn_profile', 'string', 25, array('notnull' => true, 'notblank' => true, 'default' => 'default'));
		$this->hasColumn('dbn_max_menu_attempts', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true, 'default' => 3));
		$this->hasColumn('dbn_min_search_digits', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true, 'default' => 3));
		$this->hasColumn('dbn_terminator_key', 'string', 1, array('notnull' => true, 'notblank' => true, 'default' => '#'));
		$this->hasColumn('dbn_digit_timeout', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true, 'default' => 3000));
		$this->hasColumn('dbn_max_result', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true, 'default' => 5));
		$this->hasColumn('dbn_next_key', 'string', 1, array('notnull' => true, 'notblank' => true, 'default' => '6'));
		$this->hasColumn('dbn_prev_key', 'string', 1, array('notnull' => true, 'notblank' => true, 'default' => '4'));
		$this->hasColumn('dbn_switch_order_key', 'string', 1, array('notnull' => true, 'notblank' => true, 'default' => '*'));
		$this->hasColumn('dbn_select_name_key', 'string', 1, array('notnull' => true, 'notblank' => true, 'default' => '1'));
		$this->hasColumn('dbn_new_search_key', 'string', 1, array('notnull' => true, 'notblank' => true, 'default' => '3'));
		$this->hasColumn('dbn_search_order', 'string', 10, array('notnull' => true, 'notblank' => true, 'default' => 'last_name'));
	}

	public function setUp()
	{
		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}
}

?>