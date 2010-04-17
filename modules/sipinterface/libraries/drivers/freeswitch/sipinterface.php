<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * sipinterface.php - FreeSwitch SIP Profile / interface configuration driver
 *
 * Allows for configuration of sip profiles and attaching of trunks/gateways to those sip profiles for inbound/outbound calls
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage SipInterface
 */
class FreeSwitch_SipInterface_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('sofia', 'sipinterface_' . $obj->sipinterface_id);

        $xml->update('/domains/domain[@name="all"][@alias="true"][@parse="false"]');

        // Set our internal IPs for SIP & RTP. This also defines what interface we bind to.
        if ($obj->ip_address) {
            $xml->update('/settings/param[@name="rtp-ip"]{@value="' . $obj->ip_address . '"}');
            $xml->update('/settings/param[@name="sip-ip"]{@value="' . $obj->ip_address . '"}');
        } else {
            $xml->update('/settings/param[@name="rtp-ip"]{@value="$${local_ip_v4}"}');
            $xml->update('/settings/param[@name="sip-ip"]{@value="$${local_ip_v4}"}');
        }

        if ($obj->port) {
            $xml->update('/settings/param[@name="sip-port"]{@value="' . $obj->port . '"}');
        } else {
            $xml->update('/settings/param[@name="sip-port"]{@value="5060"}');
        }

        if(!empty($obj->multiple)) {
            $xml->update('/settings/param[@name="multiple-registrations"]{@value="true"}');
        } else {
            $xml->deleteNode('/settings/param[@name="multiple-registrations"]');
        }

        // Set our external IPs for SIP & RTP
        if ($obj->ext_ip_address) {
            if ($obj->nat_type) {
                // Force external IP w/ auto-nat
                $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="autonat:' . $obj->ext_ip_address . '"}');
                $xml->update('/settings/param[@name="ext-sip-ip"]{@value="autonat:' . $obj->ext_ip_address . '"}');
            } else {
                // Force static external IP
                $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="' . $obj->ext_ip_address . '"}');
                $xml->update('/settings/param[@name="ext-sip-ip"]{@value="' . $obj->ext_ip_address . '"}');
            }
        } elseif ($obj->nat_type == 1) {
            // Automatically detect NAT and external IP using various strategies built into FS
            $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="auto-nat"}');
            $xml->update('/settings/param[@name="ext-sip-ip"]{@value="auto-nat"}');
        } elseif($obj->nat_type == 2) {
            // No IP defined and no auto-nat set... Just try to use stun to auto-detect
            $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="stun:stun.freeswitch.org"}');
            $xml->update('/settings/param[@name="ext-sip-ip"]{@value="stun:stun.freeswitch.org"}');
        } else {
            $xml->deleteNode('/settings/param[@name="ext-rtp-ip"]');
            $xml->deleteNode('/settings/param[@name="ext-sip-ip"]');
        }

        $xml->update('/settings/param[@name="auth-calls"]{@value="' . ($obj->auth ? 'true' : 'false') .'"}');

        // This may be overly optimistic but it should work in pretty much all cases
        $xml->update('/settings/param[@name="aggressive-nat-detection"][@value="true"]');

        // Turn off session timers, they are irritating and cause all sorts of issues
        $xml->update('/settings/param[@name="enable-timer"]{@value="false"}');

        $xml->update('/settings/param[@name="user-agent-string"][@value="Configured by TCAPI"]');

        if ($aclList = netlists::getListName($obj->nat_net_list_id)) {
            $xml->update('/settings/param[@name="apply-nat-acl"]{@value="' .$aclList .'"}');
        } else {
            $xml->deleteNode('/settings/param[@name="apply-nat-acl"]');
        }
        
        $xml->update('/settings/param[@name="context"]{@value="context_' . $obj->context_id . '"}');
		
        $xml->update('/settings/param[@name="rtp-timer-name"]{@value="soft"}');

        $xml->update('/settings/param[@name="codec-prefs"]{@value="$${global_codec_prefs}"}');

        $xml->update('/settings/param[@name="inbound-codec-negotiation"]{@value="generous"}');

        $xml->update('/settings/param[name="inbound-reg-force-matching-username"]{@value="true"}');

        $xml->update('/settings/param[@name="nonce-ttl"]{@value="86400"}');

        $xml->update('/settings/param[@name="manage-presence"]{@value="true"}');

        //$xml->update('/settings/param[@name="force-register-domain"]{@value="$${domain}"}');

        //$xml->update('/settings/param[@name="force-register-db-domain"]{@value="$${domain}"}');

        $xml->update('/settings/param[@name="enable-timer"]{@value="false"}');

        // Set relevant ACLs
        if ($aclList = netlists::getListName($obj->inbound_net_list_id)) {
            $xml->update('/settings/param[@name="apply-inbound-acl"]{@value="' .$aclList .'"}');
        } else {
            $xml->deleteNode('/settings/param[@name="apply-inbound-acl"]');
        }

        if ($aclList = netlists::getListName($obj->register_net_list_id)) {
            $xml->update('/settings/param[@name="apply-register-acl"]{@value="' .$aclList .'"}');
        } else {
            $xml->deleteNode('/settings/param[@name="apply-register-acl"]');
        }
    }

    public static function delete($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        
        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('sofia', 'sipinterface_' . $obj->sipinterface_id);

        $xml->deleteNode();
    }
}
