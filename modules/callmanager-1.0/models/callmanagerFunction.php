<?php defined('SYSPATH') or die('No direct access allowed.');

class callmanagerFunction extends Bluebox_Record
{
	function setTableDefinition()
	{
		$this->hasColumn('cmf_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true));
		$this->hasColumn('cmf_name', 'string', 75, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('cmf_desc', 'string', 200);
		$this->hasColumn('cmf_display_name', 'string', 200);
		$this->hasColumn('cmf_context', 'string', 200);
		$this->hasColumn('cmf_package_id', 'integer', 32, array('notnull' => true, 'unsigned' => true));
	}

	public function setUp()
	{
		$this->hasOne('package', array('local' => 'cmf_package_id', 'foreign' => 'package_id', 'onDelete' => 'CASCADE'));
		$this->actAs('GenericStructure');
		$this->actAs('Timestampable');
	}

	public static function getFunctionByName($name)
	{
		if (!$funcobj = Doctrine::getTable('callmanagerFunction')->findOneBycmf_name($name))
			throw new callmanagerException('This function is not registered', 0);
		return $funcobj;
	}

	public static function getFunctionsByContext($contextList)
	{
		$funcquery = Doctrine_Query::create()->select('*')->from('callmanagerFunction');
		foreach (explode(';', $contextList) as $context)
		{
			if (substr($context, -1, 1) != ';')
				$context .= ';';
			$funcquery->orWhere('cmf_context LIKE ?', $context . '%');
			$funcquery->orWhere('cmf_context LIKE ?', '%;' . $context . '%');
		}
		return $funcquery->execute();
	}
	
	public static function register($name, $package, $displayname, $context, $desc = null, $mode = 'SAFE')
	{
		// see if the package is already registered
		$funcobj = null;
		try {
			$funcobj =& self::getFunctionByName($name);
		} catch (Exception $e) {
			if (!$e->getCode() == 0)
				throw $e;
		}

		if ($funcobj && $mode == 'SAFE')
		{
			throw new callmanagerException('This feature is already registered and registration was attempted in safe mode.', 0);
		}

		// find the package attempting registration
		try {
			if (!$packageobj = Package_Catalog::getInstalledPackage($package))
				throw new callmanagerException('The package "' . $package . '" that attempted to register funnction "' . $name . '" was not found.', -1);
		} catch (Package_Catalog_Exception $e) {
			throw new callmanagerException('The package "' . $package . '" that attempted to register function "' . $name . '" was not found.', -1);
		}
		$package = $packageobj['datastore_id'];

		// if we are reregging (e.g. to handle a package upgrade) then make sure that package matches what is on file
		if ($mode == 'REREG' && $funcobj && $funcobj->cmf_package_id !== $package)
		{
			// Make sure the package that is on file still exists, otherwise allow the rereg
			$cmf_package_obj = Doctrine::getTable('package')->findOne($funcobj->cmf_package_id);
			if ($cmf_package_obj && $cmf_package_obj->name !== $packageobj->name)
				throw new callmanagerException('Rereg was attempted on the funnction named "' . $funcobj->cmf_name .
					'" but the package that tried to reregister named "' . $packageobj->name .
					'" does not match the currently registered package "' . $ftr_package_obj->name . '".', -2);
		}

		if (!$funcobj)
			$funcobj = new callmanagerFunction();

		$funcobj->cmf_name = $name;
		$funcobj->cmf_display_name = $displayname;
		$funcobj->cmf_package_id = $package;
		$funcobj->cmf_context = $context;
		$funcobj->cmf_desc = $desc;
		$funcobj->save();
	}

	public static function reregister($name, $package, $displayname, $context, $desc = 'null')
	{
		self::register($name, $package, $displayname, $context, $desc, 'REREG');
	}

	public static function forceregister($name, $package, $displayname, $context, $desc = 'null')
	{
		self::register($name, $package, $displayname, $context, $desc, 'FORCE');
	}

	public static function unregister($name, $package, $mode = 'SAFE')
	{
		$funcobj = Doctrine::getTable('callmanagerFunction')->findOneByname($name);
		if (!$funcobj)
			$this->_throwError('Unregistration was attempted on function "' . $name . '" that is not registered', 0);

		$packageobj = null;
		try {
			$packageobj = Package_Catalog::getInstalledPackage($package);
		} catch (Package_Catalog_Exception $e) {}
		if (!$packageobj && $mode !== 'FORCE')
			$this->_throwError('The package "' . $package . '" tried to unregister function "' . $name  . '" but the package cannot be found', -1);

		$package = $packageobj['datastore_id'];

		if ($package !== $funcobj->fcmf_package_id && $mode !== 'FORCE')
		{
			if ($packageobj)
				$packagename = $packageobj->name;
			else
				$packagename = 'NOT FOUND';

			$this->_throwError('The package "' . $packagename . '" tried to unregister "' . $funcobj->cmf_name . '" in SAFE mode but did not match the package on file', -2);
		}

		$funcobj->delete();
	}

	public static function forceunregister($name, $package)
	{
		self::unregister($name, $package, 'FORCE');
	}

	private function _throwError($errorMessage, $errorLevel = -10)
	{
		throw new callmanagerException($errorMessage, $errorLevel);
	}
}

?>