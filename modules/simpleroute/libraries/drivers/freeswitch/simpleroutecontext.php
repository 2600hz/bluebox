<?php
defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */
/**
 * trunk.php - FreeSwitch Trunk configuration driver
 *
 * Allows for configuration of inbound and outbound trunks
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage SimpleRoute
 */
class FreeSwitch_SimpleRouteContext_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj)
    {
        // TODO: This is a sort-of dumbed down routing mechanism. It is not nearly as rich as what we planned on
        // This module will be incompatible with the ultimate routing module we build
        // This module, in it's current state, allows 7, 10 and 11 digit US dialing, 011 international or 911 to go out a configured
        // trunk. Only 1 trunk will work for each type of destination, per context.
        //
        // Reference to our XML document & context
        // If you feel so inspired, the real routing module is spec'ed out here: http://www.bluebox.org/v3/wiki/RoutingModules
        // (It's a bunch of work to do)


        // emergency
        if ($obj->SimpleRoute->emergency && $pattern = simplerouter::getOutboundPattern('emergency', 'freeswitch')) {
            $xml = FreeSwitch::createExtension('trunk_' . $obj->SimpleRoute->trunk_id . '_911', 'main', 'context_' . $obj->context_id);
            $emergency = '/condition[@field="destination_number"][@expression="' . $pattern . '"][@bluebox="emergency"]';

            // TODO: Allow for a specific Caller ID on E911 calls. That would go here.
            $xml->update($emergency . '/action[@application="set"][@bluebox="prepend"]{@data="prepend=' . $obj->SimpleRoute->emergency_prepend . '"}');
            $xml->update($emergency . '/action[@application="set"][@bluebox="cid_name"]{@data="effective_caller_id_name=' . $obj->SimpleRoute->caller_id_name . '"}');
            $xml->update($emergency . '/action[@application="set"][@bluebox="cid_number"]{@data="effective_caller_id_number=' . $obj->SimpleRoute->caller_id_number . '"}');

            // If a Caller ID module is installed and caller ID is set, use it
            // TODO: Integrate this into the plugin
            $caller_id = '/condition[@field="${outbound_caller_id_number}"][@expression="^.+$"][@break="never"][@bluebox="caller_id"]';
            $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_name=${outbound_caller_id_name}"]');
            $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');

            $dummy = '/condition[@field="destination_number"][@expression="' . $pattern . '"][@bluebox="global"]';
            $xml->update($dummy . '/action[@application="bridge"]{@data="sofia\/gateway\/trunk_' . $obj->SimpleRoute->trunk_id . '\/${prepend}$1"}');
        }
        // international
        if ($obj->SimpleRoute->international && $pattern = simplerouter::getOutboundPattern('international', 'freeswitch')) {
            kohana::log('debug', 'Adding international route with rule ' . $pattern);
            $xml = FreeSwitch::createExtension('trunk_' . $obj->SimpleRoute->trunk_id . '_intl', 'main', 'context_' . $obj->context_id);
            $international = '/condition[@field="destination_number"][@expression="' . $pattern .'"][@bluebox="international"]';

            // TODO: Allow for a specific Caller ID on E911 calls. That would go here.
            $xml->update($international . '/action[@application="set"][@bluebox="prepend"]{@data="prepend=' . $obj->SimpleRoute->international_prepend . '"}');
            $xml->update($international . '/action[@application="set"][@bluebox="cid_name"]{@data="effective_caller_id_name=' . $obj->SimpleRoute->caller_id_name . '"}');
            $xml->update($international . '/action[@application="set"][@bluebox="cid_number"]{@data="effective_caller_id_number=' . $obj->SimpleRoute->caller_id_number . '"}');

            // If a Caller ID module is installed and caller ID is set, use it
            $caller_id = '/condition[@field="${outbound_caller_id_number}"][@expression="^.+$"][@break="never"][@bluebox="caller_id"]';
            $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_name=${outbound_caller_id_name}"]');
            $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');

            $dummy = '/condition[@field="destination_number"][@expression="' . $pattern . '"][@bluebox="global"]';
            $xml->update($dummy . '/action[@application="bridge"]{@data="sofia\/gateway\/trunk_' . $obj->SimpleRoute->trunk_id . '\/${prepend}$1"}');
        }
        //domestic dialing plans
        if ($obj->SimpleRoute->local && $pattern = simplerouter::getOutboundPattern('local', 'freeswitch')) {
            $xml = FreeSwitch::createExtension('trunk_' . $obj->SimpleRoute->trunk_id . '_domestic_10', 'main', 'context_' . $obj->context_id);
            $domestic = '/condition[@field="destination_number"][@expression="' . $pattern .'"][@bluebox="domestic"][@break="never"]';

            $xml->update($domestic . '/action[@application="set"][@bluebox="prepend"]{@data="prepend=' . $obj->SimpleRoute->local_prepend . '"}');
            $xml->update($domestic . '/action[@application="set"][@bluebox="cid_name"]{@data="effective_caller_id_name=' . $obj->SimpleRoute->caller_id_name . '"}');
            $xml->update($domestic . '/action[@application="set"][@bluebox="cid_number"]{@data="effective_caller_id_number=' . $obj->SimpleRoute->caller_id_number . '"}');

            // If a Caller ID module is installed and caller ID is set, use it
            $caller_id = '/condition[@field="${outbound_caller_id_number}"][@expression="^.+$"][@break="never"][@bluebox="caller_id"]';
            $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_name=${outbound_caller_id_name}"]');
            $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');

            $dummy = '/condition[@field="destination_number"][@expression="' . $pattern . '"][@bluebox="global"]';
            $xml->update($dummy . '/action[@application="bridge"]{@data="sofia\/gateway\/trunk_' . $obj->SimpleRoute->trunk_id . '\/${prepend}$1"}');

            if ($pattern = simplerouter::getOutboundPattern('short', 'freeswitch')) {
                $xml = FreeSwitch::createExtension('trunk_' . $obj->SimpleRoute->trunk_id . '_domestic_7', 'main', 'context_' . $obj->context_id);
                $local = '/condition[@field="destination_number"][@expression="' . $pattern .'"][@bluebox="local"]';

                $xml->update($local . '/action[@application="set"][@bluebox="prepend"]{@data="prepend=' . $obj->SimpleRoute->local_prepend . '"}');
                $xml->update($local . '/action[@application="set"][@bluebox="cid_name"]{@data="effective_caller_id_name=' . $obj->SimpleRoute->caller_id_name . '"}');
                $xml->update($local . '/action[@application="set"][@bluebox="cid_number"]{@data="effective_caller_id_number=' . $obj->SimpleRoute->caller_id_number . '"}');

                // If a Caller ID module is installed and caller ID is set, use it
                $caller_id = '/condition[@field="${outbound_caller_id_number}"][@expression="^.+$"][@break="never"][@bluebox="caller_id"]';
                $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_name=${outbound_caller_id_name}"]');
                $xml->update($caller_id . '/action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');

                $dummy = '/condition[@field="destination_number"][@expression="' . $pattern . '"][@bluebox="global"]';
                $xml->update($dummy . '/action[@application="bridge"]{@data="sofia\/gateway\/trunk_' . $obj->SimpleRoute->trunk_id . '\/${prepend}' . $obj->SimpleRoute->area_code . '$1"}');
            }
            
            //$tollfree = '/condition[@field="destination_number"][@expression="^(18(0{2}|8{2}|7{2}|6{2})\d{7})$"][@bluebox="local"]';
            //	$xml->update($domestic .    '/action[@application="bridge"]{@data="sofia\/gateway\/trunk_' . $obj->SimpleRoute->trunk_id . '\/${prepend}$1"}');

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
