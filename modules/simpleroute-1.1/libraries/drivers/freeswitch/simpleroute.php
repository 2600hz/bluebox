<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SimpleRoute_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if (empty($base['plugins']['simpleroute']['patterns']))
        {
            return;
        }

        $simpleroute = $base['plugins']['simpleroute'];
        $sip = $base['plugins']['sip'];

        foreach ($simpleroute['patterns'] as $simple_route_id => $options)
        {
            foreach ($simpleroute['contexts'] as $context_id => $enabled)
            {
                $xml = FreeSwitch::createExtension('trunk_' .$base['trunk_id'] .'_pattern_' .$simple_route_id, 'main', 'context_' .$context_id);

                if (empty($enabled))
                {
                    $xml->deleteNode();

                    continue;
                }

                if (empty($options['enabled']))
                {
                    $xml->deleteNode();

                    continue;
                }

                if (!$pattern = simplerouter::getOutboundPattern($simple_route_id, 'freeswitch'))
                {
                    $xml->deleteNode();

                    continue;
                }

                $xml->deleteChildren();

                $pattern_string = '';

                foreach (array_keys($pattern) AS $pattern_index)
                {
                    $pattern_string .= '|' . $pattern[$pattern_index];
                }

                $pattern_string = substr($pattern_string, 1);

                $condition = '/condition[@field="destination_number"][@expression="' .$pattern_string . '"][@bluebox="pattern_' .$simple_route_id . '_parts"]';

                if (!empty($options['prepend']))
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="prepend"]{@data="prepend=' . $options['prepend'] . '"}');
                }
                else
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="prepend"]{@data="prepend="}');
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

                // Put Caller ID into the right place
                if (isset($sip['caller_id_field'])) {
                    if (($sip['caller_id_field'] == 'rpid') or ($sip['caller_id_field'] == 'pid')) {
                        $xml->update($caller_id . '/action[@application="export"][@bluebox="caller_id_field"]{@data="sip_cid_type=' . $sip['caller_id_field'] . '"}');
                    } else {
                        // Assume Caller ID is default or elsewhere
                        $xml->deleteNode($caller_id . '/action[@application="export"][@bluebox="caller_id_field"]');
                    }
                }

                foreach (array_keys($pattern) AS $pattern_index)
                {
                    $dummy = '/condition[@field="destination_number"][@break="never"][@expression="' . $pattern[$pattern_index] . '"][@bluebox="pattern_' .$simple_route_id . '_part_' . ($pattern_index+1) .'_out"]';

                    $xml->update($dummy .'/action[@application="set"]{@data="hangup_after_bridge=true"}');

                    if (!empty($simpleroute['continue_on_fail']))
                    {
                        //$xml->update($dummy .'/action[@application="set"][@bluebox="setting_continue_on_fail"]{@data="failure_causes=NORMAL_CLEARING,ORIGINATOR_CANCEL,CRASH"}');
                        $xml->update($dummy .'/action[@application="set"][@bluebox="setting_continue_on_fail"]{@data="continue_on_fail=true"}');
                    }
                    else
                    {
                        $xml->deleteNode($dummy .'/action[@application="set"][@bluebox="setting_continue_on_fail"]');
                    }

                    $boolAllowMediaProxy = $base['registry']['allow_media_proxy']==0? 'false':'true';
                    $xml->update($dummy . '/action[@application="set"][@data="proxy_media='.$boolAllowMediaProxy.'"]');

                    $xml->update($dummy . '/action[@application="bridge"][@bluebox="out_trunk_' .$base['trunk_id'] .'"]{@data="sofia\/gateway\/trunk_' .$base['trunk_id'] . '\/${prepend}$1"}');
                }
            }
        }
    }

    public static function delete($base)
    {
        if (empty($base['plugins']['simpleroute']))
        {
            return;
        }

        $simpleroute = $base['plugins']['simpleroute'];

        foreach ($simpleroute['patterns'] as $index => $options)
        {
            foreach ($simpleroute['contexts'] as $context_id => $enabled)
            {
                $xml = FreeSwitch::createExtension('trunk_' .$base['trunk_id'] .'_pattern_' .$index, 'main', 'context_' .$context_id);

                $xml->deleteNode();
            }
        }
    }
}
