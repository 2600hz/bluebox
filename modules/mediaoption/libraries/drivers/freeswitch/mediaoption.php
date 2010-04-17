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
 * sip.php - FreeSWITCH SIP configuration driver
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage MediaOption
 */

class FreeSwitch_MediaOption_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH for SIP Devices and provide code to save SIP device specific settings for FreeSWITCH
     */
    public static function set($obj)
    {
        $base = FreePbx_Record::getBaseTransactionObject();

        // We only speak SIP here
        if (($base) and ($base->Sip)) {
            if ($base->User->Location->domain == '') {
                $domain = '$${domain}';
            } else {
                $domain = '$${location_' . $base->User->location_id . '}';
            }

            $xml = FreeSwitch::setSection('user', $domain, $base->Sip->username);

            // Set account code for this user
            $xml->update('/variables/variable[@name="bypass_media"]{@value="' . $obj->media_workaround . '"}');
        }
    }

    public static function delete($obj)
    {
        $base = FreePbx_Record::getBaseTransactionObject();

        // We only speak SIP here
        if (($base) and ($base->Sip)) {
            if ($base->User->Location->domain == '') {
                $domain = '$${domain}';
            } else {
                $domain = '$${location_' . $base->User->location_id . '}';
            }

            $xml = FreeSwitch::setSection('user', $domain, $base->Sip->username);

            $xml->deleteNode('/variables/variable[@name="bypass_media"]');
        }
    }
}
