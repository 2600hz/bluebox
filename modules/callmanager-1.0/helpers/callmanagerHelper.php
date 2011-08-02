<?php defined('SYSPATH') or die('No direct access allowed.');

class callmanagerHelper
{
	public static function getlink($function, $params, $linktype = 'image')
	{
		$callmanagementclass =  Telephony::getDriverName() . '_callmanager_Driver';
		$callmanagementobject = new $callmanagementclass();
		return $callmanagementobject->getFunctionLink($function, $params, $linktype);
	}
	
	public static function getFunctionsForCall($callinfo)
	{
		$callmanagementclass = Telephony::getDriverName() . '_callmanager_Driver';
		$callmanagementobj = new $callmanagementclass();
		return $callmanagementobj->getFunctionsForCall($callinfo);
	}
	
	public static function getFunctionLinksForCall($callinfo, $params, $linktype = 'image')
	{
		$funclinks = array();
		foreach (self::getFunctionsForCall($callinfo) as $function)
		{
			$funclinks[$function->cmf_name] = self::getlink($function->cmf_name, $params, $linktype);
		}
		return $funclinks;
	}
}
?>
