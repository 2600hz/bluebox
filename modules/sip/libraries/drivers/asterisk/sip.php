<?php
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
 * Asterisk SIP Driver
 *
 * @author Karl Anderson
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
class Asterisk_Sip_Driver extends Asterisk_Base_Driver {
    /**
     * Set SIP device specific settings for Asterisk config file types
     */
    public static function set($obj)
    {     
        // Get the asterisk driver
        $doc = Telephony::getDriver()->doc;

        // Get the base of this configure object
        $base = FreePbx_Record::getBaseTransactionObject();

        // If this is a new device add it accordingly
        if ($base instanceof SipDevice) {
            $domain = 'context_' .$base->context_id;
            
            // if this is an update of an existing user, remove the old entry
            $modified = $base->Sip->getModified(true, true);
            if (!empty($modified['username'])) {
                $doc->deleteContext('sip.conf', $modified['username']);
            }

            // Get the username of this sip device
            $sectionName = $obj->username;

            $doc->update('sip.conf', $sectionName, 'type', 'friend');
            $doc->update('sip.conf', $sectionName, 'secret', $obj->password);
            $doc->update('sip.conf', $sectionName, 'host', 'dynamic');
            $doc->update('sip.conf', $sectionName, 'context', $domain);
            $doc->update('sip.conf', $sectionName, 'subscribecontext', $domain);
        }  else if ($base instanceof Trunk) {
            $sectionName = 'trunk_' . $base->trunk_id;
            $domain = 'context_' .$base->context_id;
            
            $register = '';
            if (!empty($obj->username)) {
                $doc->update('sip.conf', $sectionName, 'username', $obj->username);
                $register .= $obj->username;
            }

            if (!empty($obj->password)) {
                $doc->update('sip.conf', $sectionName, 'secret', $obj->password);
                $register .= ':' .$obj->password;
            }

            if (!empty($register)) {                
                $register .= '@' .$base->server;

                // Add support for custom contact header
                if ($obj->contact) {
                    $register .= '/' . $obj->contact;
                }

                $register = '>' .$register;
            } else {
                $register = FALSE;
            }

            $oldValues = $obj->getModified(TRUE, TRUE);
            $oldRegister = '';
            if (!empty($oldValues['username']))
                $oldRegister .= $oldValues['username'];
            else
                $oldRegister .= $obj->username;
            if (!empty($oldValues['password']))
                $oldRegister .= ':' .$oldValues['password'];
            else
                $oldRegister .= ':' .$obj->password;
            if (!empty($oldValues['server']))
                $oldRegister .= $oldValues['server'];
            else
                $oldRegister .= '@' .$base->server;

            $doc->update('sip.conf', 'general', 'register', $oldRegister, $register);

            $doc->update('sip.conf', $sectionName, 'type', 'friend');
            $doc->update('sip.conf', $sectionName, 'host', $base->server);
            $doc->update('sip.conf', $sectionName, 'insecure', 'invite,port');  // Allow unauthenticated inbound calls
            $doc->update('sip.conf', $sectionName, 'canreinvite', 'no');
            $doc->update('sip.conf', $sectionName, 'context', $domain);
        }
    }

    public static function delete($obj)
    {
        // Get the asterisk driver
        $doc = Telephony::getDriver()->doc;
        $base = FreePbx_Record::getBaseTransactionObject();

        // get the section name, depending on the type
        if ($base instanceof SipDevice) {
            $sectionName = $obj->username;
        }  elseif ($base instanceof Trunk) {
            $sectionName = 'trunk_' . $base->trunk_id;

            $register = '';
            if (!empty($obj->username)) {
                $register .= $obj->username;
            }

            if (!empty($obj->password)) {
                $register .= ':' .$obj->password;
            }

            if (!empty($register)) {
                $register .= '@' .$base->server;
                $doc->update('sip.conf', 'general', 'register', $register, FALSE);
            }
        } else {
            return FALSE;
        }
        
        $doc->deleteContext('sip.conf', $sectionName);
    }
}
