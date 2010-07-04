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
* K Anderson
*
*/
/**
 * ringgroup.php - FreeSwitch SIP Profile / interface configuration driver
 *
 *
 * @author Michael Phillips
 * @license MPL
 * @package Bluebox
 * @subpackage RingGroup
 */
class FreeSwitch_RingGroup_Driver extends FreeSwitch_Base_Driver
{
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($obj)
    {
        $xml = Telephony::getDriver()->xml;
        // Set the root
        $domain = '$${location_' . $obj->location_id . '}';
        FreeSwitch::setSection('ringgroup', $domain, 'ringgroup_' . $obj->ring_group_id);
        $xml->deleteChildren(); //clear out all of the group members
        $ringGroupMembers = $obj->RingGroupMember->toArray();
        foreach($ringGroupMembers as $ringGroupMember) {
            $device = Doctrine::getTable('Device')->findOneByDeviceId($ringGroupMember['device_id']);
            if (empty($device)) continue;
            $xml->update(sprintf('/users/user[@id="%s"][@type="pointer"]', $device->Sip->username));
        }

        // Force a dialplan update for Ring Groups. This is required if we've changed any settings.
    }
    public static function delete($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        // Set the root
        $domain = '$${location_' . $obj->location_id . '}';
        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('ringgroup', $domain, 'ringgroup_' . $obj->ring_group_id);
        $xml->deleteNode();
    }

    public static function dialplan($obj)
    {
        $xml = Telephony::getDriver()->xml;
        kohana::log('debug', 'Saving ring group to dialplan');
        $dest = 'ringgroup_' . $obj->RingGroup->ring_group_id;
        $timeout = (isset($obj->options['timeout']) ? $obj->options['timeout'] : 30);
        $strategy = ($obj->RingGroup->strategy == 2) ? ':order' : '';

        $xml->update('/action[@application="set"][@bluebox="rg_hangup_after_bridge"]{@data="hangup_after_bridge=true"}');
        $xml->update('/action[@application="set"][@bluebox="rg_continue_on_fail"]{@data="continue_on_fail=true"}');
        $xml->update('/action[@application="set"][@bluebox="rg_call_timeout"]{@data="call_timeout=' . $timeout . '"}');
        $xml->update('/action[@application="bridge"]{@data="{leg_timeout=' . $obj->options['timeout'] . ',ignore_early_media=true}${group(call:' . $dest . '@$${location_' . $obj->RingGroup->location_id . '}' . $strategy . ')}"}');

        // Nobody answered?
        // Grab the destination we're routing to. We don't care which context we end up in per say, as long as it's the right destination.
        // TODO: Handle contexts better?
        if (!empty($obj->RingGroup->fallback_number_id) and $obj->RingGroup->FallbackNumber->NumberContext) {
            $context = $obj->RingGroup->FallbackNumber->NumberContext[0]->context_id;
            $xml->update('/action[@application="transfer"]{@data="' . $obj->RingGroup->FallbackNumber->number . ' XML context_' . $context. '"}');
        }

        // NOTE: Unlike the other functions above, $obj in THIS function is actually of type Number
        // Since your object MUST be related to Number in order to get this far anyway, so just do
        // $obj->YourObject to get the related data
        //
        // For example, to get the ring group ID, do $obj->RingGroup->ring_group_id (NOT $obj->ring_group_id, which is what
        // you would do in the other functions)

    }
}
