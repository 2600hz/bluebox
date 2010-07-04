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
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * sip.php - FreeSWITCH SIP configuration driver
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Sip
 */

class FreeSwitch_Sip_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH for SIP Devices and provide code to save SIP device specific settings for FreeSWITCH
     */
    public static function set($obj)
    {

        $base = Bluebox_Record::getBaseTransactionObject();

        // Reference to our XML document
        $driver = Telephony::getDriver();
        $xml = $driver->xml;

        if ($base instanceof Device) {
            $domain = '$${location_' . $base->User->location_id . '}';

            // Did we just change the username? If yes, we need to do a rename, which is special...
            $modified = $base->Sip->getLastModified(TRUE);
            if (isset($modified['username'])) {
                // Get a reference to the old username section and change the username
                $xml->setXmlRoot(FreeSwitch::getSectionPath('user', $domain, $modified['username']));
                $xml->setAttributeValue('', 'id', $base->Sip->username);
            }

            FreeSwitch::setSection('user', $domain, $base->Sip->username);

            $xml->update('/params/param[@name="password"]{@value="' . $obj->password . '"}');
            $xml->update('/params/param[@name="dial-string"]{@value="' . '{presence_id=${dialed_user}@${dialed_domain}}${sofia_contact(${dialed_user}@${dialed_domain})}"}');

            if ($base->context_id > 0) {
                $xml->update('/variables/variable[@name="user_context"]{@value="context_' . $base->context_id . '"}');
                $xml->update('/variables/variable[@name="force_transfer_context"]{@value="context_' . $base->context_id . '"}');    // In most cases, the call transfer context should match the default context
            } else {
                $xml->deleteNode('/variables/variable[@name="user_context"]');
                $xml->deleteNode('/variables/variable[@name="force_transfer_context"]');
            }

            // Temp xml to help control toll fraud until context work done
            $xml->update('/variables/variable[@name="toll_allow"]{@value="domestic"}');

            // Set account code for this user
            $xml->update('/variables/variable[@name="accountcode"]{@value="' . $obj->username . '"}');
        } elseif ($base instanceof Trunk) {
            if (($base->SipInterfaceTrunk) and ($base->SipInterfaceTrunk->sipinterface_id)) {
                FreeSwitch::setSection('gateway', 'sipinterface_' . $base->SipInterfaceTrunk->sipinterface_id, 'trunk_' . $base->trunk_id);

                if ($obj->username) {
                    $xml->update('/param[@name="username"]{@value="' . $obj->username . '"}');
                    $xml->update('/param[@name="password"]{@value="' . $obj->password . '"}');
                    
                    // Assume we want to register if user/pass is specified
                    // TODO: Make this a checkbox?
                    $xml->update('/param[@name="register"]{@value="true"}');
                } else {
                    // Assume we don't want to register if no user/pass is specified
                    $xml->update('/param[@name="register"]{@value="false"}');

                    // Have to put something, so we'll just make it generic
                    $xml->update('/param[@name="username"]{@value="TCAPI_User"}');
                    $xml->update('/param[@name="password"]{@value="TCAPI_User"}');
                }

                if($obj->contact) {
                    $xml->update('/settings/param[@name="extension"]{@value="' . $obj->contact . '"}');
                    $xml->update('/settings/param[@name="extension-in-contact"]{@value="true"}');
                } else {
                    $xml->deleteNode('/settings/param[@name="extension"]');
                    $xml->deleteNode('/settings/param[@name="extension-in-contact"]');
                }
            }
        } else {
            kohana::log('error', 'Skipping sip config because unknown class ' . get_class($base));
        }
    }

    public static function delete($obj)
    {
        $base = Bluebox_Record::getBaseTransactionObject();

        if ($base instanceof Device) {
            // Set the root
            if ($base->User->Location->domain == '') {
                $domain = '$${domain}';
            } else {
                $domain = '$${location_' . $base->User->location_id . '}';
            }
            $xml = FreeSwitch::setSection('user', $domain, $obj->username);

            $xml->deleteNode();

        } elseif ($base instanceof Trunk) {
            $xml = FreeSwitch::setSection('gateway', 'sipinterface_' . $base->SipInterfaceTrunk->sipinterface_id, 'trunk_' . $base->trunk_id);

            $xml->deleteNode('/param[@name="username"][@value="' . $obj->username . '"]');
            $xml->deleteNode('/param[@name="password"][@value="' . $obj->password . '"]');
        }
    }
}
