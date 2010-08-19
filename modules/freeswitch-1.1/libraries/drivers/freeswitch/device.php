<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Device_Driver extends FreeSwitch_Base_Driver
{
    public static function set($device)
    {
        
    }

    public static function delete($device)
    {
        
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        if ($destination instanceof Device)
        {
            $domain = '$${location_' .$destination['User']['location_id'] .'}';

            $numberOptions = fs::getNumberOptions($number);

            $xml->update('/action[@application="set"][@bluebox="settingTimeout"]{@data="call_timeout=' .$numberOptions['timeout'] .'"}');

            $xml->update('/action[@application="set"][@bluebox="settingRing"]{@data="ringback=' . $numberOptions['ringtype'] . '"}');

            $xml->update('/action[@application="set"][@bluebox="settingRingXfer"]{@data="transfer_ringback=' . $numberOptions['ringtype'] . '"}');

            if (!empty($destination['plugins']['callerid']['internal_name']))
            {
                $xml->update('/action[@application="export"][@bluebox="sipCalleeIdName"]{@data="sip_callee_id_name=' .$destination['plugins']['callerid']['internal_name'] .'"}');
            }
            else
            {
                $xml->update('/action[@application="export"][@bluebox="sipCalleeIdName"]{@data="sip_callee_id_name=' .$destination['name'] .'"}');
            }
            
            $xml->update('/action[@application="export"][@bluebox="sipCalleeIdNumber"]{@data="sip_callee_id_number=' .$number['number'] .'"}');

            $xml->update('/action[@application="bridge"]{@data="user\/' .$destination['plugins']['sip']['username'] .'@' .$domain .'"}');
        }
    }
}