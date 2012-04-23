<?php defined('SYSPATH') or die('No direct access allowed.');
class FaxDispositionManager
{
	public static function __callStatic($funcname, $params = null)
	{
		if ($params == null)
	        $number = Event::$data;
		else
			$number = $params[0];
		
        $faxProfile = $number['Destination'];			
		$dispobj = Doctrine::getTable('FaxDisposition')->findOneBy('fxd_id', $faxProfile['fxp_fxd_id']);
			
		$dispositionDriver = Telephony::getDriverName() . '_' . $dispobj->name . '_Driver';

		if (count($params) > 0)
		{
			return call_user_func_array($dispositionDriver . '::' . $funcname, $params);
		}
		else
		{
			return call_user_func($dispositionDriver . '::' . $funcname);
		}
	}
}
?>