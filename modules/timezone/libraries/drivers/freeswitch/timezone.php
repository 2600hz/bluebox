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
 * timezone.php - FreeSWITCH Timezone driver
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Timezone
 */
class FreeSwitch_Timezone_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot('//document/section[@name="directory"]/domain/users');

        if ($obj instanceOf Device) {
            // The section we are working with is <document><section name="directory"><domain><users><user id=XXX>
            $prefix = sprintf('/user[@id="%s"]', $obj->Device->Sip->username);

            // Create the base user record (using the defined prefix).
            $xml->set($prefix);

            // These vars are made up by this library. They are used consistently throughout.
            $xml->update($prefix . '/variables/variable[@name="timezone"]{@value="' . str_replace('/', '\/', $obj->timezone) . '"}');
        }
    }

    public static function delete($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        
        if ($obj instanceOf Device) {
            $xml->setXmlRoot(sprintf('//document/section[@name="directory"]/domain/users/user[@id="%s"]', $obj->Device->Sip->username));

            $xml->deleteNode('/variables/variable[@name="timezone"]');
        }
    }
}
