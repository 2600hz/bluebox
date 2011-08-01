<?php defined('SYSPATH') or die('No direct access allowed.');

class featureDriverRegistry
{
	public static $drivers = null;
	public static $moduleDrivers = null;

	static function init($mode = 'SAFE')
	{
		if ((self::$featureDrivers != null || self::$moduleFeatures) && $mode == 'SAFE')
			throw new featureException('Driver registry has already been built and init is running in safe mode', 0);
		self::$drivers = array();
		self::$moduleDrivers = array();
		Event::run('featureDriver.register');
	}

	public static function register($modulename, $drivername, $displayname, $mode = 'SAFE')
	{
		$driverkey = $modulename . '.' . $drivername;
		if (isset(self::$drivers[$driverkey]) && $mode == 'SAFE')
			throw new featureException('A driver named ' , $drivername . ' from module ' . $modulename . ' already exists in the registry.', -1);
		self::$drivers[$driverkey] = array('module' => $modulename, 'driver' => $drivername, 'display_name' => $displayname);
		self::$moduleDrivers[$modulename] = array($drivername => $displayname);
	}

	public static function unregister($modulename, $drivername, $displayname)
	{
		$driverkey = $modulename . '.' . $drivername;
		if (!isset(self::$drivers[$driverkey]))
			throw new featureException('A driver named ' , $drivername . ' from module ' . $modulename . ' was not found in the registry.', 0);
		unset(self::$drivers[$driverkey]);
		unset(self::$moduleDrivers[$modulename][$drivername]);
	}

	public static function getDriverList()
	{
		try {
			self::init();
		} catch (featureException $e) {
			if ($e->getCode != 0)
				throw $e;
		}

		return self::$drivers;
	}

	public static function getDriverListByModule($modulename)
	{
		try {
			self::init();
		} catch (featureException $e) {
			if ($e->getCode != 0)
				throw $e;
		}

		if (!isset(self::$moduleDrivers[$modulename]))
			throw new featureException('No drivers registered for module ' . $modulename, 0);

		return self::$moduleDrivers[$modulename];
	}

	public static function whatprovides($drivername)
	{
		$returnlist = array();
		foreach (self::$drivers as $driverkey => $driverinfo)
		{
			if ($driverinfo['driver'] === $drivername)
				$returnlist[] = $driverinfo['module'];
		}
		return $returnlist;
	}
}
?>