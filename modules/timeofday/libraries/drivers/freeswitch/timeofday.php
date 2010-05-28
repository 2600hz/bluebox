<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_TimeOfDay_Driver extends FreeSwitch_Base_Driver {

    public static function set($obj)
    {

    }

    public static function delete($obj)
    {

    }

    public static function dialplan($obj)
    {
        $xml = Telephony::getDriver()->xml;

        if (!empty($obj->TimeOfDay->time)) {
            $parts = explode(';', $obj->TimeOfDay->time);
            if (count($parts) != 2) {
                kohana::log('error', 'Time was not comprised of two parts');
                return FALSE;
            }
            
            if ($parts[0] == $parts[1]) {
                $time = $parts;
            } else {
                $time = $parts[0] .'-' .$parts[1];
            }
        } else {
            kohana::log('error', 'Time was not empty');
            return FALSE;
        }

        $action = dialplan::getTransfer($obj->TimeOfDay->during_number_id);
        $action = str_replace('transfer ', '', $action);

        $antiAction = dialplan::getTransfer($obj->TimeOfDay->outside_number_id);
        $antiAction = str_replace('transfer ', '', $antiAction);


        $weekDayColumns = array('sun', 'mon', 'tue', 'wen', 'thur', 'fri', 'sat');
        $wday = '';
        foreach($weekDayColumns as $pos => $weekDayColumn) {
            if (!empty($obj->TimeOfDay->$weekDayColumn)) {
                $wday .= $pos+1 .',';
            }
        }
        $wday = rtrim($wday, ',');

        if (empty($wday)) {
            $wday = '1-7';
        }

        $xml->setXmlRoot($xml->getExtensionRoot());
        $xml->update('/condition[@wday="' .$wday .'"]{@minute-of-day="' .$time .'"}');
        $xml->update('/condition[@wday="' .$wday .'"]/action[@application="transfer"]{@data="' .$action .'"}');
        $xml->update('/condition[@wday="' .$wday .'"]/anti-action[@application="transfer"]{@data="' .$antiAction .'"}');
    }
}
