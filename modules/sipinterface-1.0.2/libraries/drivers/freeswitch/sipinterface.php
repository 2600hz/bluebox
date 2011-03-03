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
    public static function set($base)
    {
        if ($base instanceof Location)
        {
            $alias_sipinterface_id = arr::get($base, 'plugins', 'sipinterface', 'sipinterface_id');

            self::update_aliases($base['location_id'], $alias_sipinterface_id);
        }

        if (!($base instanceof SipInterface))
        {
            return TRUE;
        }

        $allow_seperate_media_ip = kohana::config('sipinterface.allow_seperate_media_ip');

        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('sofia', 'sipinterface_' . $base['sipinterface_id']);

        $xml->deleteNode('/domains');

        // Turn off session timers, they are irritating and cause all sorts of issues
        $xml->update('/settings/param[@name="enable-timer"]{@value="false"}');

        if ($user_agent = kohana::config('sipinterface.user_agent'))
        {
            $xml->update('/settings/param[@name="user-agent-string"]{@value="' .kohana::config('sipinterface.user_agent') .'"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="user-agent-string"]');
        }
        
        $xml->update('/settings/param[@name="rtp-timer-name"]{@value="soft"}');

        $xml->update('/settings/param[@name="codec-prefs"]{@value="$${global_codec_prefs}"}');

        $xml->update('/settings/param[@name="inbound-codec-negotiation"]{@value="generous"}');

        $xml->update('/settings/param[@name="inbound-reg-force-matching-username"]{@value="true"}');

        $xml->update('/settings/param[@name="nonce-ttl"]{@value="86400"}');

        $xml->update('/settings/param[@name="rfc2833-pt"]{@value="101"}');

        $xml->update('/settings/param[@name="manage-presence"]{@value="true"}');

        $xml->update('/settings/param[@name="enable-timer"]{@value="false"}');

        $xml->update('/settings/param[@name="auth-calls"]{@value="' . ($base['auth'] ? 'true' : 'false') .'"}');

        // Set our internal IPs for SIP. This also defines what interface we bind to.
        if ($ip_address = arr::get($base, 'ip_address'))
        {
            $xml->update('/settings/param[@name="sip-ip"]{@value="' .$ip_address .'"}');
        }
        else
        {
            $xml->update('/settings/param[@name="sip-ip"]{@value="$${local_ip_v4}"}');
        }

        // If we are seperating the media and sip IPs then get the current
        // media internal IP otherwise use the same as SIP
        if ($allow_seperate_media_ip)
        {
            $ip_address = arr::get($base, 'registry', 'media_ip_address');
        }

        // Set our internal IPs for RTP. This also defines what interface we bind to.
        if ($ip_address)
        {
            $xml->update('/settings/param[@name="rtp-ip"]{@value="' .$ip_address .'"}');
        }
        else
        {
            $xml->update('/settings/param[@name="rtp-ip"]{@value="$${local_ip_v4}"}');
        }

        // If the user has a port defined then use it otherwise use 5060
        if ($port = arr::get($base, 'port'))
        {
            $xml->update('/settings/param[@name="sip-port"]{@value="' .$port . '"}');
        } 
        else
        {
            $xml->update('/settings/param[@name="sip-port"]{@value="5060"}');
        }

        // check if multiple-registrations per credintial should be enabled
        if(arr::get($base, 'multiple'))
        {
            $xml->update('/settings/param[@name="multiple-registrations"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="multiple-registrations"]');
        }

        // should we ping registered devices?
        if(arr::get($base, 'registry', 'options_ping'))
        {
            $xml->update('/settings/param[@name="nat-options-ping"]{@value="true"}');

            $xml->update('/settings/param[@name="all-reg-options-ping"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="nat-options-ping"]');

            $xml->deleteNode('/settings/param[@name="all-reg-options-ping"]');
        }

        // Set our external IPs for SIP & RTP
        if ($ext_ip_address = arr::get($base, 'ext_ip_address'))
        {
            if ($base['nat_type'])
            {
                // Force external IP w/ auto-nat
                $xml->update('/settings/param[@name="ext-sip-ip"]{@value="autonat:' . $ext_ip_address. '"}');
            } 
            else
            {
                // Force static external IP
                $xml->update('/settings/param[@name="ext-sip-ip"]{@value="' . $ext_ip_address .'"}');
            }
        } 
        elseif ($base['nat_type'] == 1)
        {
            // Automatically detect NAT and external IP using various strategies built into FS
            $xml->update('/settings/param[@name="ext-sip-ip"]{@value="auto-nat"}');
        } 
        elseif($base['nat_type'] == 2)
        {
            // No IP defined and no auto-nat set... Just try to use stun to auto-detect
            $xml->update('/settings/param[@name="ext-sip-ip"]{@value="stun:stun.freeswitch.org"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="ext-sip-ip"]');
        }

        // if we are allowing sip and rtp to be on different IPs then get the
        // external address of the media, otherwise use the same as SIP
        if ($allow_seperate_media_ip)
        {
            $ext_ip_address = arr::get($base, 'registry', 'media_ext_ip_address');
        }

        // Set our external IPs for RTP
        if ($ext_ip_address)
        {
            if ($base['nat_type'])
            {
                $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="autonat:' . $ext_ip_address . '"}');
            }
            else
            {
                $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="' . $ext_ip_address . '"}');
            }
        }
        elseif ($base['nat_type'] == 1)
        {
            // Automatically detect NAT and external IP using various strategies built into FS
            $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="auto-nat"}');
        }
        elseif($base['nat_type'] == 2)
        {
            // No IP defined and no auto-nat set... Just try to use stun to auto-detect
            $xml->update('/settings/param[@name="ext-rtp-ip"]{@value="stun:stun.freeswitch.org"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="ext-rtp-ip"]');
        }

        // NAT detection settings for registrations
        if (arr::get($base, 'registry', 'detect_nat_on_registration'))
        {
            $xml->update('/settings/param[@name="aggressive-nat-detection"]{@value="true"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="aggressive-nat-detection"]');
        }

        // NDLB / forced rport for crappy devices/setups
        if (arr::get($base, 'registry', 'force_rport'))
        {
            $xml->update('/settings/param[@name="NDLB-force-rport"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="NDLB-force-rport"]');
        }

        // Enable log auth failures by default.
        if (arr::get($base, 'registry', 'log_auth_failures'))
        {
            $xml->update('/settings/param[@name="log-auth-failures"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="log-auth-failures"]');
        }

        // Enable compact headers by default. With all the Codecs FS now supports we see lots of
        // bad behavior re: UDP packets that are too large and get fragmented
        if (arr::get($base, 'registry', 'compact_headers'))
        {
            $xml->update('/settings/param[@name="enable-compact-headers"]{@value="true"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="enable-compact-headers"]');
        }

        // Find the context id that we should direct unauthed calls to
        if ($context_id = arr::get($base, 'context_id'))
        {
            $xml->update('/settings/param[@name="context"]{@value="context_' .$context_id . '"}');
        }
        else
        {
            $xml->update('/settings/param[@name="context"]{@value="' .FreeSwitch::AUTO_ROUTE_CONTEXT .'"}');
        }

        // If there is a forced domain set it up now
        if ($force_domain = arr::get($base, 'registry', 'force_register_domain'))
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
        if ($aclList = netlists::getListName($base['nat_net_list_id']))
        {
            $xml->update('/settings/param[@name="apply-nat-acl"]{@value="' .$aclList .'"}');
        }
        else
        {
            $xml->deleteNode('/settings/param[@name="apply-nat-acl"]');
        }
        
        if ($aclList = netlists::getListName($base['inbound_net_list_id']))
        {
            $xml->update('/settings/param[@name="apply-inbound-acl"]{@value="' .$aclList .'"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="apply-inbound-acl"]');
        }

        if ($aclList = netlists::getListName($base['register_net_list_id']))
        {
            $xml->update('/settings/param[@name="apply-register-acl"]{@value="' .$aclList .'"}');
        } 
        else
        {
            $xml->deleteNode('/settings/param[@name="apply-register-acl"]');
        }

        $locations = Doctrine::getTable('Location')->findAll();

        foreach ($locations as $location)
        {
            $alias_sipinterface_id = arr::get($location, 'plugins', 'sipinterface', 'sipinterface_id');

            self::update_aliases($location['location_id'], $alias_sipinterface_id);
        }
        
        if ($default_sipinterface_id = SipInterface::get_default('sipinterface_id'))
        {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' .$default_sipinterface_id);

            $xp = new DOMXPath($xml);

            $elements = $xp->query($xml->preUpdate(''));

            if (count($elements) == 1 AND ($node = $elements->item(0)))
            {
                $node = $elements->item(0);
                
                $parentNode = $node->parentNode;

                $parentNode->removeChild($node);

                $parentNode->appendChild($node);

                $xml->update('/domains/domain[@name="all"][@alias="true"][@parse="false"]');
            }
        }
    }

    public static function delete($base)
    {
        if ($base instanceof Location)
        {
            self::update_aliases($base['location_id']);
        }

        if (!($base instanceof SipInterface))
        {
            return TRUE;
        }
        
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        
        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        FreeSwitch::setSection('sofia', 'sipinterface_' . $base['sipinterface_id']);

        $xml->deleteNode();
    }

    public static function update_aliases($location_id, $alias_sipinterface_id = NULL)
    {
        $location_name = '$${location_' .$location_id .'}';

        $sipinterfaces = Doctrine::getTable('SipInterface')->findAll();

        foreach($sipinterfaces as $sipinterface)
        {
            $sipinterface_id = $sipinterface['sipinterface_id'];

            $xml = FreeSwitch::setSection('sofia_aliases', 'sipinterface_' .$sipinterface_id);

            if ($sipinterface_id == $alias_sipinterface_id)
            {
                $xml->update('/alias[@name="' .$location_name .'"]');
            }
            else
            {
                $xml->deleteNode('/alias[@name="' .$location_name .'"]');
            }
        }
    }

}
