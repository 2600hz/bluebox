<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_ValetParkingLot_Driver extends FreeSwitch_Base_Driver
{
    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $xml->update('/action[@application="valet_park"]{@data="' . $destination['vpl_name'] . ' auto in ' . $destination['vpl_start'] . ' ' . $destination['vpl_end'] . '"}');
    }
}
?>