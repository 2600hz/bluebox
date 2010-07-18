<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SimpleRoute_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if (empty($base['plugins']['simpleroute']))
        {
            return;
        }

        $simpleroute = $base['plugins']['simpleroute'];

        foreach ($simpleroute['patterns'] as $index => $options)
        {
            if (empty($options['enabled']))
            {
                continue;
            }

            if (!$pattern = simplerouter::getOutboundPattern($index, 'freeswitch'))
            {
                continue;
            }

            foreach ($simpleroute['contexts'] as $context_id => $enabled)
            {
                $xml = FreeSwitch::createExtension('trunk_' .$base['trunk_id'] .'_pattern_' .$index, 'main', 'context_' .$context_id);

                if (empty($enabled))
                {


                    continue;
                }
                
                $condition = '/condition[@field="destination_number"][@expression="' .$pattern . '"][@bluebox="pattern_' .$index .'"]';

                if (!empty($options['prepend']))
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="prepend"]{@data="prepend=' .$options['prepend'] . '"}');
                }
                else
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="prepend"]{@data="prepend=' .$options['prepend'] . '"}');
                }

                if (!empty($simpleroute['caller_id_name']))
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="cid_name"]{@data="effective_caller_id_name=' .$simpleroute['caller_id_name'] .'"}');
                }

                if (!empty($simpleroute['caller_id_number']))
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="cid_number"]{@data="effective_caller_id_number=' .$simpleroute['caller_id_number'] .'"}');
                }

                // If a Caller ID module is installed and caller ID is set, use it
                // TODO: Integrate this into the plugin
                $caller_id = '/condition[@field="${outbound_caller_id_number}"][@expression="^.+$"][@break="never"][@bluebox="caller_id"]';

                $xml->update($caller_id .'/action[@application="set"][@data="effective_caller_id_name=${outbound_caller_id_name}"]');

                $xml->update($caller_id .'/action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');

                $dummy = '/condition[@field="destination_number"][@expression="' . $pattern . '"][@bluebox="pattern_' .$index .'_out"]';

                $xml->update($dummy . '/action[@application="bridge"]{@data="sofia\/gateway\/trunk_' .$base['trunk_id'] . '\/${prepend}$1"}');
            }
        }
    }
    public static function delete($obj)
    {
        $base = Bluebox_Record::getBaseTransactionObject();

        if (empty($base->trunk_id)) {
                return FALSE;
        }

        // Delete the whole darn extension for each type of extension
        $xml = FreeSwitch::createExtension('trunk_' . $base->trunk_id . '_911', 'main', 'context_' . $obj->context_id);
        $xml->deleteNode();

        $xml = FreeSwitch::createExtension('trunk_' . $base->trunk_id . '_intl', 'main', 'context_' . $obj->context_id);
        $xml->deleteNode();

        $xml = FreeSwitch::createExtension('trunk_' . $base->trunk_id . '_domestic_10', 'main', 'context_' . $obj->context_id);
        $xml->deleteNode();

        $xml = FreeSwitch::createExtension('trunk_' . $base->trunk_id . '_domestic_7', 'main', 'context_' . $obj->context_id);
        $xml->deleteNode();

    }
}
