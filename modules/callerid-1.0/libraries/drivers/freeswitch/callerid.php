<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_CallerId_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if (empty($base['plugins']['callerid']))
        {
            return;
        }

        $callerid = $base['plugins']['callerid'];

        if ($base instanceof Conference)
        {
            FreeSwitch::section('conference_profile', $base['conference_id']);

            if (!empty($callerid['external_number']))
            {
                $xml->update('/param[@name="caller-id-number"]{@value="' .$callerid['external_number'] .'"}');
            }
        } 
        elseif ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            // These vars are made up by this library. They are used consistently throughout.
            if (!empty($callerid['external_name']))
            {
                $xml->update('/variables/variable[@name="outbound_caller_id_name"]{@value="' .$callerid['external_name'] .'"}');
            }

            if (!empty($callerid['external_number']))
            {
                $xml->update('/variables/variable[@name="outbound_caller_id_number"]{@value="' .$callerid['external_number'] .'"}');
            }

            if (!empty($callerid['internal_name']))
            {
                $xml->update('/variables/variable[@name="internal_caller_id_name"]{@value="' .$callerid['internal_name'] .'"}');
            }

            if (!empty($callerid['internal_number']))
            {
                $xml->update('/variables/variable[@name="internal_caller_id_number"]{@value="' .$callerid['internal_number'] .'"}');
            }
        }
    }

    public static function delete($base)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        if ($base instanceof Conference)
        {
            FreeSwitch::section('conference_profile', $base['conference_id']);

            $xml->deleteNode('/param[@name="caller-id-number"]');
        }
        elseif ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->deleteNode('/variables/variable[@name="outbound_caller_id_name"]');

            $xml->deleteNode('/variables/variable[@name="outbound_caller_id_number"]');

            $xml->deleteNode('/variables/variable[@name="internal_caller_id_name"]');

            $xml->deleteNode('/variables/variable[@name="internal_caller_id_number"]');
        }
    }

    public static function conditioning()
    {
        $xml = FreeSWITCH::createExtension('callerid');

        $condition = '/condition[@field="${internal_caller_id_number}"][@expression="^.+$"]';

        $xml->update($condition . '/action[@application="set"][@data="effective_caller_id_name=${internal_caller_id_name}"]');

        $xml->update($condition .'/action[@application="set"][@data="effective_caller_id_number=${internal_caller_id_number}"]');
    }

    public static function preNumber()
    {
        $number = Event::$data;
        
        $xml = Telephony::getDriver()->xml;
                
        if (empty($number['Destination']['plugins']['callerid']))
        {
            return;
        }

        if (!empty($number['Destination']['plugins']['callerid']['internal_name']))
        {
            $internal_name = $number['Destination']['plugins']['callerid']['internal_name'];

            $xml->update('/action[@application="set"][@bluebox="callerid_internal_name"][@data="effective_caller_id_name=' .$internal_name .'"]');
        }
        else
        {
            $xml->deleteNode('/action[@application="set"][@bluebox="callerid_internal_name"]');
        }

        if (!empty($number['Destination']['plugins']['callerid']['internal_number']))
        {
            $internal_number = $number['Destination']['plugins']['callerid']['internal_number'];

            $xml->update('/action[@application="set"][@bluebox="callerid_internal_number"][@data="effective_caller_id_number=' .$internal_number .'"]');
        }
        else
        {
            $xml->deleteNode('/action[@application="set"][@bluebox="callerid_internal_number"]');
        }

        if (!empty($number['Destination']['plugins']['callerid']['external_name']))
        {
            $external_name = $number['Destination']['plugins']['callerid']['external_name'];

            $xml->update('/action[@application="set"][@bluebox="callerid_external_name"][@data="effective_caller_id_name=' .$external_name .'"]');
        }
        else
        {
            $xml->deleteNode('/action[@application="set"][@bluebox="callerid_external_name"]');
        }

        if (!empty($number['Destination']['plugins']['callerid']['external_number']))
        {
            $external_number = $number['Destination']['plugins']['callerid']['external_number'];

            $xml->update('/action[@application="set"][@bluebox="callerid_external_number"][@data="effective_caller_id_number=' .$external_number .'"]');
        }
        else
        {
            $xml->deleteNode('/action[@application="set"][@bluebox="callerid_external_number"]');
        }
    }
}
