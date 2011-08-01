<?php defined('SYSPATH') or die('No direct access allowed.');

class Feature extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('ftr_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('ftr_name', 'string', 75, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('ftr_desc', 'string', 200);
		$this->hasColumn('ftr_display_name', 'string', 200);
		$this->hasColumn('ftr_package_id', 'integer', 32, array('notnull' => true, 'unsigned' => true));
		$this->hasColumn('ftr_edit_user_type', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => user::TYPE_NORMAL_USER));
	}

	public function setUp()
	{
		$this->hasOne('package', array('local' => 'ftr_package_id', 'foreign' => 'package_id', 'onDelete' => 'CASCADE'));

		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
		$this->actAs('MultiTenant');
	}

    public function contains($fieldName)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::contains('ftr_display_name');
    	else
    		return parent::contains($fieldName);
    }
	
	public function get($fieldName, $load = true)
    {
    	if (strtolower($fieldName) == 'name')
    		return parent::get('ftr_display_name', $load);
    	else
    		return parent::get($fieldName, $load);
    }

	public static function getFeatureByName($name)
	{
		if (!$featobj = Doctrine::getTable('feature')->findOneByftr_name($name))
			throw new featureException('This feature is not registered', 0);
		return $featobj;
	}

	public static function register($name, $package, $displayname, $desc = null, $edit_user_type = user::TYPE_NORMAL_USER, $mode = 'SAFE')
	{
		// see if the package is already registered
		$featobj = null;
		try {
			$featobj =& self::getFeatureByName($name);
		} catch (Exception $e) {
			if (!$e->getCode() == 0)
				throw $e;
		}

		if ($featobj && $mode == 'SAFE')
		{
			throw new featureException('This feature is already registered and registration was attempted in safe mode.', 0);
		}

		// find the package attempting registration
		try {
			if (!$packageobj = Package_Catalog::getInstalledPackage($package))
				throw new featureException('The package "' . $package . '" that attempted to register feature "' . $name . '" was not found.', -1);
		} catch (Package_Catalog_Exception $e) {
			throw new featureException('The package "' . $package . '" that attempted to register feature "' . $name . '" was not found.', -1);
		}
		$package = $packageobj['datastore_id'];

		// if we are reregging (e.g. to handle a package upgrade) then make sure that package matches what is on file
		if ($mode == 'REREG' && $featobj && $featobj->ftr_package_id !== $package)
		{
			// Make sure the package that is on file still exists, otherwise allow the rereg
			$ftr_package_obj = Doctrine::getTable('package')->findOne($featobj->ftr_package_id);
			if ($ftr_package_obj && $ftr_package_obj->name !== $packageobj->name)
				throw new featureException('Rereg was attempted on the feature named "' . $featobj->ftr_name .
					'" but the package that tried to reregister named "' . $packageobj->name .
					'" does not match the currently registerd feature named "' . $ftr_package_obj->name . '".', -2);
		}

		if (!$featobj)
			$featobj = new feature();

		$featobj->ftr_name = $name;
		$featobj->ftr_display_name = $displayname;
		$featobj->ftr_package_id = $package;
		$featobj->ftr_desc = $desc;
		$featobj->ftr_edit_user_type = $edit_user_type;
		$featobj->save();
	}

	public static function reregister($name, $package, $displayname, $desc = 'null', $edit_user_type = user::TYPE_NORMAL_USER)
	{
		self::register($name, $package, $displayname, $desc, $edit_user_type, 'REREG');
	}

	public static function forceregister($name, $package, $displayname, $desc = 'null', $edit_user_type = user::TYPE_NORMAL_USER)
	{
		self::register($name, $package, $displayname, $desc, $edit_user_type, 'FORCE');
	}

	public static function unregister($name, $package, $mode = 'SAFE')
	{
		$featobj = Doctrine::getTable('feature')->findOneByname($name);
		if (!$featobj)
			throw new featureException('Unregistration was attempted on feature "' . $name . '" that is not registered', 0);

		$packageobj = null;
		try {
			$packageobj = Package_Catalog::getInstalledPackage($package);
		} catch (Package_Catalog_Exception $e) {}
		if (!$packageobj && $mode !== 'FORCE')
			throw new featureException('The package "' . $package . '" tried to unregister feature "' . $name  . '" but the package cannot be found', -1);

		$package = $packageobj['datastore_id'];

		if ($package !== $featobj->ftr_package_id && $mode !== 'FORCE')
		{
			if ($packageobj)
				$packagename = $packageobj->name;
			else
				$packagename = 'NOT FOUND';

			throw new featureException('The package "' . $packagename . '" tried to unregister "' . $featobj->ftr_name . '" in SAFE mode but did not match the package on file', -2);
		}

		$featobj->delete();
	}

	public static function forceunregister($name, $package)
	{
		self::unregister($name, $package, 'FORCE');
	}
}

?>