<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_TimeOfDay_Driver extends FreeSwitch_Base_Driver {

    public static function set($timeOfDay)
    {

    }

    public static function delete($timeOfDay)
    {

    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        if (!empty($destination['time']))
        {
            $parts = explode(';', $destination['time']);

            if (count($parts) != 2)
            {
                kohana::log('error', 'Time was not comprised of two parts');

                return FALSE;
            }

            if ($parts[0] == $parts[1])
            {
                $time = $parts;
            }
            else
            {
                $time = $parts[0] .'-' .$parts[1];
            }
        }
        else
        {
            kohana::log('error', 'Time of day route had no time');
            
            return FALSE;
        }

        $weekDayColumns = array('sun', 'mon', 'tue', 'wen', 'thur', 'fri', 'sat');

        $wday = '';

        foreach($weekDayColumns as $pos => $weekDayColumn)
        {
            if (!empty($destination[$weekDayColumn]))
            {
                $wday .= $pos + 1 .',';
            }
        }

        $wday = rtrim($wday, ',');

        // TODO: This makes no sense....
        if (empty($wday))
        {
            $wday = '1-7';
        }

        $xml->setXmlRoot($xml->getExtensionRoot());

        $xml->update('/condition[@wday="' .$wday .'"]{@minute-of-day="' .$time .'"}');

        if($action = fs::getTransfer($destination['during_number_id']))
        {
            $action = str_replace('transfer ', '', $action);

            $xml->update('/condition[@wday="' .$wday .'"]/action[@application="transfer"]{@data="' .$action .'"}');
        }
        else
        {
            $xml->update('/condition[@wday="' .$wday .'"]/action[@application="hangup"]');
        }

        if ($antiAction = fs::getTransfer($destination['outside_number_id']))
        {
            $antiAction = str_replace('transfer ', '', $antiAction);
            
            $xml->update('/condition[@wday="' .$wday .'"]/anti-action[@application="transfer"]{@data="' .$antiAction .'"}');
        }
        else
        {
            $xml->update('/condition[@wday="' .$wday .'"]/action[@application="hangup"]');
        }
    }
}
