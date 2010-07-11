<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_RingGroup_Driver extends FreeSwitch_Base_Driver
{
    public static function set($ringgroup)
    {
    }

    public static function delete($ringgroup)
    {
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        switch($destination['strategy'])
        {
            case RingGroup::STRATEGY_SEQUENTIAL:
                $memberSeperator = '|';
            
                break;

            case RingGroup::STRATEGY_ENTERPRISE:
                $memberSeperator = ':_:';

                break;

            case RingGroup::STRATEGY_PARALLEL:
            default:
                $memberSeperator = ',';

        }
        

        $numberOptions = fs::getNumberOptions($number);

        $xml->update('/action[@application="set"][@bluebox="settingEndBridge"][@data="hangup_after_bridge=true"]');

        $xml->update('/action[@application="set"][@bluebox="settingTimeout"]{@data="call_timeout=' .$numberOptions['timeout'] .'"}');

        $xml->update('/action[@application="set"][@bluebox="settingRing"]{@data="ringback=${' . $numberOptions['ringtype'] . '}"}');

        $xml->update('/action[@application="set"][@bluebox="settingRingXfer"]{@data="transfer_ringback=${' . $numberOptions['ringtype'] . '}"}');

        $dialstring = '';

        foreach ($destination['members'] as $member)
        {
            if (!empty($member['options']))
            {
                $dialstring .= '{';

                foreach ($member['options'] as $parameter => $value)
                {
                    $dialstring .= $parameter .'=' .$value .',';
                }

                $dialstring = rtrim($dialstring, ',') .'}';
            }
            
            $dialstring .= $member['bridge'] .$memberSeperator;
        }

        $dialstring = substr($dialstring, 0, strlen($memberSeperator) * -1);

        $dialstring = str_replace('/', '\/', $dialstring);
        $dialstring = str_replace('@', '\@', $dialstring);

        $xml->update('/action[@application="bridge"]{@data="' .$dialstring .'"}');
    }
}
