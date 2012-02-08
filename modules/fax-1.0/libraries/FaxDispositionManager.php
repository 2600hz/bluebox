<?php defined('SYSPATH') or die('No direct access allowed.');
ini_set('xdebug.var_display_max_depth', 1);

function get_var_dump(&$var)
{
/*	ob_start();
	var_dump($var);
	return ob_get_flush();
*/	
	$retstr = '';
	foreach ($var as $key => $value)
	{
		$retstr .= $key . ', ';
	}
	return $retstr;
	
}

class FaxDispositionManager
{
	public static function __callStatic($funcname, $params = null)
	{
		Kohana::log('debug', $funcname);
		
		if ($params == null)
	        $number = Event::$data;
		else
			$number = $params[0];
		
        $faxProfile = $number['Destination'];			
		$dispobj = Doctrine::getTable('FaxDisposition')->findOneBy('fxd_id', $faxProfile['fxp_fxd_id']);
			
		$dispositionDriver = Telephony::getDriverName() . '_' . $dispobj->name . '_Driver';

		if (count($params) > 0)
		{
			Kohana::log('debug', 'Calling ' . $dispositionDriver . '::' . $funcname . ' with params.');
			return call_user_func_array($dispositionDriver . '::' . $funcname, $params);
		}
		else
		{
			Kohana::log('debug', 'Calling ' . $dispositionDriver . '::' . $funcname . ' no params.');
			return call_user_func($dispositionDriver . '::' . $funcname);
		}
	}
}
?>