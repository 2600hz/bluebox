<?php defined('SYSPATH') or die('No direct access allowed.');
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
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
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
 * @package Bluebox
 * @subpackage SipInterface
 */
class FreeSwitch_SipInterface_Driver extends FreeSwitch_Base_Driver
{
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($sipinterface)
    {
        if (!($sipinterface instanceof SipInterface))
        {
            return TRUE;
        }

        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('sofia', 'sipinterface_' . $sipinterface['sipinterface_id']);

        $xml->update('/domains/domain[@name="all"][@alias="true"][@parse="false"]');

        // Turn off session timers, they are irritating and cause all sorts of issues
        $xml->update('/settings/param[@name="enable-timer"]{@value="false"}');

        $xml->update('/settings/param[@name="user-agent-string"][@value="Configured by 2600hz"]');

        $xml->update('/settings/param[@name="rtp-timer-name"]{@value="soft"}');

        $xml->update('/settings/param[@name="codec-prefs"]{@value="$${global_codec_prefs}"}');

        $xml->update('/settings/param[@name="inbound-codec-negotiation"]{@value="generous"}');

        $xml->update('/settings/param[@name="inbound-reg-force-matching-username"]{@value="true"}');

        $xml->update('/settings/param[@name="nonce-ttl"]{@value="86400"}');

        $xml->update('/settings/param[@name="rfc2833-pt"]{@value="101"}');

        $xml->update('/settings/param[@name="manage-presence"]{@value="true"}');

        $xml->update('/settings/param[@name="enable-timer"]{@value="false"}');

        $xml->update('/settings/param[@name="auth-calls"]{@value="' . ($sipinterface['auth'] ? 'true' : 'false') .'"}');

        // Set our internal IPs for SIP & RTP. This also defines what interface we bind to.
        if ($ip_address = arr::get($sipinterface, 'ip_address'))
        {
            $sip_ip_address = $ip_address;

            $xml->update('/settings/param[@name="sip-ip"]{@value="' .$ip_address .'"}');
        }
        else
        {
            $xml->update('/settings/param[@name="sip-ip"]{@value="$${local_ip_v4}"}');
        }


        if (($ip_address = arr::get($sipinterface, 'registry', 'media_ip_address')) OR ($ip_address = arr::get($sipinterface, 'ip_address')))
        {
            $media_ip_address = $ip_address;

            $xml->update('/settings/param[@name="rtp-ip"]{@value="' .$ip_address .'"}');
        }
        else
        {
            $xml->update('/settings/param[@name="rtp-ip"]{@value="$${local_ip_v4}"}');
        }


        // If the user has a port defined then use it otherwise use 5060
        if ($port = arr::get($sipinterface, 'port'))
        {
            $xml->update('/settings/param[@name="sip-port"]{@value="' .$port . '"}');
        } 
        else
        {
            $xml->update('/settings/param[@name="sip-port"]{@value="5060"}');
        }

        // check if multiple-registrations per credintial should be enabled
        if(arr::get($sipinterface, 'multiple'))
        {
            $xml->update('/settings/param[@name="multiple-registrations"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="multiple-registrations"]');
        }

        // should we ping registered devices?
        if(arr::get($sipinterface, 'registry', 'all_reg_options_ping'))
        {
            $xml->update('/settings/param[@name="all-reg-options-ping"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="all-reg-options-ping"]');
        }

        // Set our external IPs for SIP & RTP
        if (!empty($sip_ip_address))
        {
            if ($sipinterface['nat_type'])
            {
                // Force external IP w/ auto-nat
                $xml->update('/settings/param[@name="ext-sip-ip"]{@value="autonat:' . $sip_ip_address. '"}');

                if (!empty($media_ip_address))
                {
                    $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="autonat:' . $media_ip_address . '"}');
                }
            } 
            else
            {
                // Force static external IP
                $xml->update('/settings/param[@name="ext-sip-ip"]{@value="' . $sip_ip_address .'"}');

                if (!empty($media_ip_address))
                {
                    $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="' . $media_ip_address . '"}');
                }
            }
        } 
        elseif ($sipinterface['nat_type'] == 1)
        {
            // Automatically detect NAT and external IP using various strategies built into FS
            $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="auto-nat"}');

            $xml->update('/settings/param[@name="ext-sip-ip"]{@value="auto-nat"}');
        } 
        elseif($sipinterface['nat_type'] == 2)
        {
            // No IP defined and no auto-nat set... Just try to use stun to auto-detect
            $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="stun:stun.freeswitch.org"}');
            
            $xml->update('/settings/param[@name="ext-sip-ip"]{@value="stun:stun.freeswitch.org"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="ext-rtp-ip"]');
            
            $xml->deleteNode('/settings/param[@name="ext-sip-ip"]');
        }

        // NAT detection settings for registrations
        if (arr::get($sipinterface, 'registry', 'detect_nat_on_registration'))
        {
            $xml->update('/settings/param[@name="aggressive-nat-detection"]{@value="true"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="aggressive-nat-detection"]');
        }

        // NDLB / forced rport for crappy devices/setups
        if (arr::get($sipinterface, 'registry', 'force_rport'))
        {
            $xml->update('/settings/param[@name="NDLB-force-rport"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="NDLB-force-rport"]');
        }

        // Enable compact headers by default. With all the Codecs FS now supports we see lots of
        // bad behavior re: UDP packets that are too large and get fragmented
        if (arr::get($sipinterface, 'registry', 'compact_headers'))
        {
            $xml->update('/settings/param[@name="enable-compact-headers"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="enable-compact-headers"]');
        }

        // Find the context id that we should direct unauthed calls to
        if ($context_id = arr::get($sipinterface, 'context_id'))
        {
            $xml->update('/settings/param[@name="context"]{@value="context_' .$context_id . '"}');
        }
        else
        {
            $xml->update('/settings/param[@name="context"]{@value=default_public"}');
        }

        // If there is a forced domain set it up now
        if ($force_domain = arr::get($sipinterface, 'registry', 'force_register_domain'))
        {
            $force_location = '$${location_' .$force_domain .'}';

            $xml->update('/settings/param[@name="force-register-domain"]{@value="$${location_' .$force_domain .'}"}');

            $xml->update('/settings/param[@name="force-register-db-domain"]{@value="$${location_' .$force_domain .'}"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="force-register-domain"]');

            $xml->deleteNode('/settings/param[@name="force-register-db-domain"]');
        }

        // Set relevant ACLs
        if ($aclList = netlists::getListName($sipinterface['nat_net_list_id']))
        {
            $xml->update('/settings/param[@name="apply-nat-acl"]{@value="' .$aclList .'"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="apply-nat-acl"]');
        }
        
        if ($aclList = netlists::getListName($sipinterface['inbound_net_list_id']))
        {
            $xml->update('/settings/param[@name="apply-inbound-acl"]{@value="' .$aclList .'"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="apply-inbound-acl"]');
        }

        if ($aclList = netlists::getListName($sipinterface['register_net_list_id']))
        {
            $xml->update('/settings/param[@name="apply-register-acl"]{@value="' .$aclList .'"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="apply-register-acl"]');
        }
    }

    public static function delete($sipinterface)
    {
        if (!($sipinterface instanceof SipInterface))
        {
            return TRUE;
        }
        
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        
        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('sofia', 'sipinterface_' . $sipinterface['sipinterface_id']);

        $xml->deleteNode();
    }
}
