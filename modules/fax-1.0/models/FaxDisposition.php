<?php defined('SYSPATH') or die('No direct access allowed.');

class FaxDisposition extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('fxd_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('fxd_name', 'string', 75, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('fxd_desc', 'string', 200);
		$this->hasColumn('fxd_display_name', 'string', 200);
		$this->hasColumn('fxd_package_id', 'integer', 32, array('notnull' => true, 'unsigned' => true));
	}

	public function setUp()
	{
		$this->hasOne('package', array('local' => 'fxd_package_id', 'foreign' => 'package_id', 'onDelete' => 'CASCADE'));
		$this->hasMany('FaxProfile', array('local' => 'fxd_id', 'foreign' => 'fxp_fxd_id', 'onDelete' => 'CASCADE'));
		
		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('MultiTenant');
	}

    public function contains($fieldName)
    {
    	switch (strtolower($fieldName))
    	{
    		case 'name':
    			return parent::contains('fxd_name');
    			break;
    		case 'id':
    			return parent::contains('fxd_id');
    			break;
    		default:
    			return parent::contains($fieldName);
    			break;
    	}
	}
    
	public function get($fieldName, $load = true)
    {
    	switch (strtolower($fieldName))
    	{
    		case 'name':
    			return parent::get('fxd_name', $load);
    			break;
    		case 'id':
    			return parent::get('fxd_id', $load);
    			break;
    		default:
    			return parent::get($fieldName, $load);
    			break;
    	}
    }

	public static function getDispositionByName($name)
	{
		if (!$dispobj = Doctrine::getTable('FaxDisposition')->findOneBy('fxd_name', $name))
			throw new faxException('This fax disposition is not registered', 0);
		return $dispobj;
	}

	public static function register($name, $package, $displayname, $desc = null, $mode = 'SAFE')
	{
		// see if the package is already registered
		$dispobj = null;
		try {
			$dispobj =& self::getDispositionByName($name);
		} catch (Exception $e) {
			if (!$e->getCode() == 0)
				throw $e;
		}

		if ($dispobj && $mode == 'SAFE')
		{
			throw new faxException('This fax disposition is already registered and registration was attempted in safe mode.', 0);
		}

		// find the package attempting registration
		try {
			if (!$packageobj = Package_Catalog::getInstalledPackage($package))
				throw new faxException('The package "' . $package . '" that attempted to register fax disposition "' . $name . '" was not found.', -1);
		} catch (Package_Catalog_Exception $e) {
			throw new faxException('The package "' . $package . '" that attempted to register fax disposition "' . $name . '" was not found.', -1);
		}
		$package = $packageobj['datastore_id'];

		// if we are reregging (e.g. to handle a package upgrade) then make sure that package matches what is on file
		if ($mode == 'REREG' && $dispobj && $dispobj->fxd_package_id !== $package)
		{
			// Make sure the package that is on file still exists, otherwise allow the rereg
			$fxd_package_obj = Doctrine::getTable('package')->findOne($dispobj->fxd_package_id);
			if ($fxd_package_obj && $fxd_package_obj->name !== $packageobj->name)
				throw new faxException('Rereg was attempted on the fax disposition named "' . $dispobj->fxd_name .
					'" but the package that tried to reregister named "' . $packageobj->name .
					'" does not match the currently registerd fax disposition named "' . $fxd_package_obj->name . '".', -2);
		}

		if (!$dispobj)
			$dispobj = new FaxDisposition();

		$dispobj->fxd_name = $name;
		$dispobj->fxd_display_name = $displayname;
		$dispobj->fxd_package_id = $package;
		$dispobj->fxd_desc = $desc;
		$dispobj->save();
	}

	public static function reregister($name, $package, $displayname, $desc = 'null')
	{
		self::register($name, $package, $displayname, $desc, 'REREG');
	}

	public static function forceregister($name, $package, $displayname, $desc = 'null')
	{
		self::register($name, $package, $displayname, $desc, 'FORCE');
	}

	public static function unregister($name, $package, $mode = 'SAFE')
	{
		$dispobj = Doctrine::getTable('FaxDispositon')->findOneByname($name);
		if (!$dispobj)
			throw new faxException('Unregistration was attempted on fax disposition "' . $name . '" that is not registered', 0);

		$packageobj = null;
		try {
			$packageobj = Package_Catalog::getInstalledPackage($package);
		} catch (Package_Catalog_Exception $e) {}
		if (!$packageobj && $mode !== 'FORCE')
			throw new faxException('The package "' . $package . '" tried to unregister fax disposition "' . $name  . '" but the package cannot be found', -1);

		$package = $packageobj['datastore_id'];

		if ($package !== $dispobj->fxd_package_id && $mode !== 'FORCE')
		{
			if ($packageobj)
				$packagename = $packageobj->name;
			else
				$packagename = 'NOT FOUND';

			throw new faxException('The package "' . $packagename . '" tried to unregister "' . $dispobj->fxd_name . '" in SAFE mode but did not match the package on file', -2);
		}

		$dispobj->delete();
	}

	public static function forceunregister($name, $package)
	{
		self::unregister($name, $package, 'FORCE');
	}
	
    public static function dictionary($addopt = null)
    {
    	$reclist = Doctrine::getTable('FaxDisposition')->findAll();
    	$dict = array();
    	foreach ($reclist as $currec)
    	{
   			$dict[$currec->fxd_id] = $currec->fxd_display_name;
    	}
    	if (is_array($addopt))
    		$dict = $addopt + $dict;
    	return $dict;
    }
}

?>