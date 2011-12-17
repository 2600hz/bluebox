<?php defined('SYSPATH') or die('No direct access allowed.');

class FaxProfile extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('fxp_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('fxp_name', 'string', 75, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('fxp_desc', 'string', 200);
        $this->hasColumn('fxp_default', 'boolean', array('default' => false));
        $this->hasColumn('fxp_send', 'boolean', array('default' => false));
        $this->hasColumn('fxp_ecm_mode', 'integer', array('default' => 1));
        $this->hasColumn('fxp_t38_mode', 'integer', array('default' => 2));
        $this->hasColumn('fxp_v17_mode', 'integer', array('default' => 1));
        $this->hasColumn('fxp_force_caller', 'integer', 1, array('default' => '3'));
        $this->hasColumn('fxp_start_page', 'integer', 5, array('default' => -1));
        $this->hasColumn('fxp_end_page', 'integer', 5, array('default' => -1));
        $this->hasColumn('fxp_ident', 'string', 200, array('default' => 'Fax Number'));
        $this->hasColumn('fxp_header', 'string', 200, array('default' => 'Freeswitch Fax'));
        $this->hasColumn('fxp_prefix', 'string', 50, array('default' => 'infax'));
        $this->hasColumn('fxp_verbose', 'integer', array('default' => 2));
        $this->hasColumn('fxp_spool_dir', 'string', 254, array('default' => '/tmp/'));
        $this->hasColumn('fxp_fxd_id', 'integer', 11, array('unsigned' => true));
	}

	public function setUp()
	{
		$this->hasMany('FaxProfileNumber as Number', array('local' => 'fxp_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));
		$this->hasOne('FaxDisposition', array('local' => 'fxp_fxd_id', 'foreign' => 'fxd_id', 'onDelete' => 'CASCADE', 'owningSide' => FALSE));
		
		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}

    public function contains($fieldName)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::contains('fxp_name');
    	elseif (strtolower($fieldName) == 'id')
    		return parent::contains('fxp_id');
    	else
    		return parent::contains($fieldName);
    }
	
	public function get($fieldName, $load = true)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::get('fxp_name', $load);
    	elseif (strtolower($fieldName) == 'id')
    		return parent::get('fxp_id', $load);
    	else
    		return parent::get($fieldName, $load);
    }

    public static function dictionary($addopt = null)
    {
    	$reclist = Doctrine::getTable(get_called_class())->findAll();
    	$dict = array();
    	foreach ($reclist as $currec)
    	{
    		if ($currec->fxp_default == false)
    			$dict[$currec->id] = $currec->name;
    	}
    	if (is_array($addopt))
    		$dict = $addopt + $dict;
    	return $dict;
    }
    
    public function save($inSave = false)
    {
    	if (substr($this->fxp_spool_dir, -1) != '/')
    		$this->fxp_spool_dir += '/';
    	
		if ($inSave == false && $this->fxp_default == false)
		{
			$defaultlist = Doctrine_Query::create()
				->from('FaxProfile')
				->where('fxp_default = ?', true)
				->andWhere('fxp_id != ?', $this->fxp_id)
				->execute();
				
			if (count($defaultlist) < 1)
				$this->fxp_default = true;
		}
		
    	parent::save();
    	
    	if ($inSave == false && $this->fxp_default == true)
    	{
			$defaultlist = Doctrine_Query::create()
				->from('FaxProfile')
				->where('fxp_default = ?', true)
				->andWhere('fxp_id != ?', $this->fxp_id)
				->execute();
				
    		foreach ($defaultlist as $defobj)
	    	{
	    		$defobj->fxp_default = false;
	    		$defobj->save(true);
	    	}
    	}
    	
    	if ($inSave == false && $this->fxp_send == true)
    	{
			$detectlist = Doctrine_Query::create()
				->from('FaxProfile')
				->where('fxp_send = ?', true)
				->andWhere('fxp_id != ?', $this->fxp_id)
				->execute();
				
	       	foreach ($detectlist as $detprof)
	    	{
	    		$detprof->fxp_send = false;
	    		$detprof->save(true);
	    	}
    	}
    }
}

?>