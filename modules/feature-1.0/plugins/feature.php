<?php defined('SYSPATH') or die('No direct access allowed.');
class feature_Plugin extends Bluebox_Plugin
{
	function setupCreateEdit()
	{
		$catalog = Package_Catalog::getPackageList();
		$packagelist = array();
		foreach ($catalog as $packagename => $info)
		{
			try {
				if ($package = Package_Catalog::getInstalledPackage($packagename))
					$packagelist[$package['datastore_id']] = $package['displayName'];
			} catch (Package_Catalog_Exception $e) {}
		}
		asort($packagelist);
		$packagelist = array(0 => 'Select a Package...') + $packagelist;
		Event::$data->template->content->packagelist = $packagelist;
		Event::$data->template->content->usertypelist = array(0 => 'Guest', 40 => 'Restricted', 50 => 'Normal', 60 => 'Power', 70 => 'Restricted Admin', 80 => 'Account Admin', 101 => 'System Admin');
		return true;
	}
}

?>