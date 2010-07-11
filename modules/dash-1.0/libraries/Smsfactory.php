 <?php
class Smsfactory
{
	public static function setDriver($type)
	{
		if (include_once('SmsDrivers/' . $type . '.php')) {
			$classname = 'Driver_' . $type;
			return new $classname;
		} else {
			return false;
		}
	}
}
