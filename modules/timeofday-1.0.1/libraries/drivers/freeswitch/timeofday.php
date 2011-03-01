<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_TimeOfDay_Driver extends FreeSwitch_Base_Driver
{
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

            $offset = timezone::getUTCOffset($destination['plugins']['timezone']['timezone']) / 60;

            if ($parts[0] == $parts[1])
            {
                $times[] = $parts - $offset;
            }
            else
            {
                $times[] = $parts[0] - $offset;
                $times[] = $parts[1] - $offset;
            }
        }
        else
        {
            kohana::log('error', 'Time of day route had no time');
            
            return FALSE;
        }

        $weekDayColumns = array('sun', 'mon', 'tue', 'wen', 'thur', 'fri', 'sat');

        $wday = array();

        foreach($weekDayColumns as $pos => $weekDayColumn)
        {
            if (!empty($destination[$weekDayColumn]))
            {
                $wday[] = $pos + 1;
            }
        }

        // TODO: This makes no sense....
        if (empty($wday))
        {
            $wday = array(1,2,3,4,5,6,7);
        }

        $xml->setXmlRoot($xml->getExtensionRoot());

        Kohana::log('debug', 'NODE?');
        foreach($xml->childNodes as $node)
        {
            if($node !== $xml->firstChild)
            {
                $node->deleteNode();
            }
        }

        $time_xml = "";

        foreach($times as $index => $time)
        {
            if($time < 0)
            {
                foreach($wday as $day)
                {
                    if($day == 1)
                    {
                        $offsetwday[] = 7;
                    }
                    else
                    {
                        $offsetwday[] = $day - 1;
                    }
                }

                $time_xml .= self::createTimeCondition($destination, $offsetwday, (1440 + $time) . '-1440');

                $times[$index] = 0;
            }

            if($time > 1440)
            {
                foreach($wday as $day)
                {
                    if($day == 7)
                    {
                        $offsetwday[] = 1;
                    }
                    else
                    {
                        $offsetwday[] = $day + 1;
                    }
                }

                $time_xml .= self::createTimeCondition($destination, $offsetwday, '0-' . ($time - 1440));

                $times[$index] = 1440;
            }
        }

        if(sizeof($times) == 1)
        {
            $time_xml .= self::createTimeCondition($destination, $wday, $times[0]);
        }
        else if(sizeof($times) == 2)
        {
            $time_xml .= self::createTimeCondition($destination, $wday, $times[0] . '-' . $times[1]);
        }

        if(!empty($time_xml))
        {
            $newXmlFragment = $xml->createDocumentFragment();

            $newXmlFragment->appendXML($time_xml);

            $xml->set('')->appendChild($newXmlFragment);
        }
        else
        {
            $xml->deleteNode();
        }
    }

    private static function createTimeCondition($destination, $wday, $time)
    {
        //$xml->update('/condition[@wday="' .$wday .'"]{@minute-of-day="' . $time .'"}');
        $wday = preg_replace('/,$/', '', implode(',', $wday));

        $xml = '<condition wday="' . $wday . '" minute-of-day="' . $time . '">';

        if(($action = fs::getTransferToNumber($destination['during_number_id'])))
        {
            $action = str_replace('transfer ', '', $action);

            //$xml->update('/condition[@wday="' . $wday .'"]/action[@application="transfer"]{@data="' .$action .'"}');
            $xml .= '<action application="transfer" data="' . $action . '"/>';
        }
        else
        {
            //$xml->update('/condition[@wday="' . $wday .'"]/action[@application="hangup"]');
            $xml .= '<action application="hangup"/>';
        }
        
        $xml .= '</condition>';

        return $xml;
    }
}
