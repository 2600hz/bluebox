<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_TimeOfDay_Driver extends FreeSwitch_Base_Driver
{
    public static function set($timeOfDay)
    {

    }

    public static function delete($timeOfDay)
    {

    }

/*
This function, given 2 numbers (low & high) will return a regular expression
that matches any low, high, and any number in between.
NOTE: THIS WORKS ONLY IF ALL 3 NUMBERS ARE THE SAME LENGTH.
It works really great on times (HHMM, e.g. 0059 for nearly 1am, or HHMMSS 235959).
NOTE 2: It should be anchored - if you have a plain number, you can just
prefix '^' to the regex. It's not done here, as you may want to have
something else in front of it.
*/
    public static function make_range_regex ($low,$high)
    {
	if ($low==$high) {
		return $low;
	}
	$lpat=''; $rpat='';
	$comp=strlen($high)-1;
	while ((substr($low,0,$comp) != substr($high,0,$comp)) && ($low<$high)) {
		$lpat.='|'.substr($low,0,$comp).'['.substr($low,$comp,1).'-9]';
		$rpat=substr($high,0,$comp).'[0-'.substr($high,$comp,1).']|'.$rpat;
		$low=sprintf("%0${comp}d",$low/10+1);
		$high=sprintf("%0${comp}d",$high/10-1);
		$comp--;
	}
	if ($high>=$low) {
		$lpat.="|".substr($low,0,$comp).'['.substr($low,$comp,1).'-'.substr($high,$comp,1).']';
	}
	$pat=substr($lpat,1).'|'.substr($rpat,0,-1);
	$pat=preg_replace('/\[0-9\]/','',$pat);
	$pat=preg_replace('/\[(\d)-\1\]/','$1',$pat);
	return "($pat)";
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];
	if ($destination['timezone']=='') {
		$timezone=Kohana::config('locale.timezone');
	} else {
		$timezone=$destination['timezone'];
	}

        if (!empty($destination['time']))
        {
            $parts = explode(';', $destination['time']);

            if (count($parts) != 2)
            {
                kohana::log('error', 'Time was not comprised of two parts');

                return FALSE;
            }
	    $time = FreeSwitch_TimeOfDay_Driver::make_range_regex (
	    	sprintf("%02d%02d",$parts[0]/60,$parts[0]%60),
	    	sprintf("%02d%02d",$parts[1]/60,$parts[1]%60)
	    );
        }
        else
        {
            kohana::log('error', 'Time of day route had no time');
            
            return FALSE;
        }

        $weekDayColumns = array('mon', 'tue', 'wen', 'thur', 'fri', 'sat', 'sun');

        $wday = '';
	$comment = '';

        foreach($weekDayColumns as $pos => $weekDayColumn)
        {
            if (!empty($destination[$weekDayColumn]))
            {
                $wday .= $pos + 1;
		$comment .= $weekDayColumn.", ";
            }
        }

        // TODO: This makes no sense....
        if (empty($wday))
        {
            $wday = '1-7';
            $comment = 'any day, ';
        }
	$comment.=sprintf("%02d:%02d - %02d:%02d",$parts[0]/60,$parts[0]%60,$parts[1]/60,$parts[1]%60);

        $xml->setXmlRoot($xml->getExtensionRoot());


	$cond='/condition[@field="${strftime_tz('.str_replace('/','\/',$timezone).' %u%H%M)}"]';
        $xml->update($cond.'{@blueboxcomment="'.$comment.'"}{@expression="^['."$wday]$time".']"}');

        if($action = fs::getTransferToNumber($destination['during_number_id']))
        {
            $action = str_replace('transfer ', '', $action);

            $xml->update($cond.'/action[@application="transfer"]{@data="' .$action .'"}');
        }
        else
        {
            $xml->update($cond.'/action[@application="hangup"]');
        }

        if ($antiAction = fs::getTransferToNumber($destination['outside_number_id']))
        {
            $antiAction = str_replace('transfer ', '', $antiAction);
            
            $xml->update($cond.'/anti-action[@application="transfer"]{@data="' .$antiAction .'"}');
        }
        else
        {
            $xml->update($cond.'/anti-action[@application="hangup"]');
        }
    }
}
