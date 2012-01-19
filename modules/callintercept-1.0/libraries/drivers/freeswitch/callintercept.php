<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_CallIntercept_Driver
{
    public static function set($base)
    {
        if(($base instanceof Device) AND ($location_id = arr::get($base['User'], 'location_id')))
        {
            $domain = '$${location_' .$location_id .'}';

            $xml = Telephony::getDriver()->xml;

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            if (($outbound_intercept_group = arr::get($base, 'plugins', 'callintercept', 'outbound_intercept_group')) != NULL)
            {
                $outbound_intercept_group = implode('|', preg_split('/[,\s\/\\\|\.]/', $outbound_intercept_group, NULL, PREG_SPLIT_NO_EMPTY));

                $xml->update('/variables/variable[@name="interceptgroup"]{@value="' . $outbound_intercept_group . '"}');
            }
            else
            {
                $xml->deleteNode('/variables/variable[@name="interceptgroup"]');
            }
        }
    }

    public static function delete($base)
    {

    }

    public static function preNumber()
    {
        $xml = Telephony::getDriver()->xml;

        $number = Event::$data;

        $destination = $number['Destination'];

        if(($callintercept = arr::get($destination, 'plugins', 'callintercept')) == NULL)
        {
            return;
        }

        if(empty($callintercept['inbound_intercept_group']))
        {
            return;
        }

        Kohana::log('debug', 'Adding call intercept line...');

        $hash_cmd = 'insert\/intercept_' . $destination['account_id'] . '\/' . $callintercept['inbound_intercept_group'] . '\/${uuid}';

        $xml->update('/action[@application="hash"][@data="' . $hash_cmd . '"]');
    }
}
