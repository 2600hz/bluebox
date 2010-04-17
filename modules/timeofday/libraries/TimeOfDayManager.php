<?php
class TimeOfDayManager
{
	public static function isDayActive($wdays, $wday)
	{

		$days = explode(',',  $wdays);
		
		return in_array($wday, $days);

	}
	
	public static function getRoutesTo($number_id)
	{
                if(empty($number_id)) {
                    return FALSE;
                }
		$time_based_route = Doctrine::getTable('TimeOfDay')->findOneByNumberId($number_id); 
		if(!$time_based_route)
		{
			return 0;
		} else {
			return 	$time_based_route->routes_to;	
		}
	}
	
	
	public function getMinuteOfTheDay($hour, $minute, $pm)
	{
		$minute_of_the_day = 0;
		
		if($hour != "12")
		{
			$minute_of_the_day += ((int)$hour * 60);
		}
		
		if($pm == 'pm')
		{
				$minute_of_the_day += (12 * 60);
		}

                $minute_of_the_day += $minute;
		
		return $minute_of_the_day;
	}
	
	public function getTimeFromMinute($minute)
	{
		$pm = 'am';
		$hour = floor($minute / 60);
		$minute = ($minute % 60);
		
		if($hour >= 12)
		{
			$hour = $hour-12;
			$pm = 'pm';
		}
		return array($hour, $minute, $pm);
	}
}
