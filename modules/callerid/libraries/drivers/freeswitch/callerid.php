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
 * callerid.php - FreeSwitch CallerId driver
 * Adds CallerId variables to FreeSWITCH XML configuration files.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage CallerId
 */
class FreeSwitch_CallerId_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        $base = Bluebox_Record::getBaseTransactionObject();

        if ($base instanceof Conference) {
            FreeSwitch::section('conference_profile', $base->conference_id);

            $xml->update('/param[@name="caller-id-name"]{@value="' . $obj->external_name . '"}');
            $xml->update('/param[@name="caller-id-number"]{@value="' . $obj->external_number . '"}');
        } elseif ($base instanceof Device) {
            if ($base->User->Location->domain == '') {
                $domain = '$${domain}';
            } else {
                $domain = '$${location_' . $base->User->location_id . '}';
            }
            FreeSwitch::setSection('user', $domain, $base->Sip->username);

            // These vars are made up by this library. They are used consistently throughout.
            $xml->update('/variables/variable[@name="outbound_caller_id_name"]{@value="' . $obj->external_name . '"}');
            $xml->update('/variables/variable[@name="outbound_caller_id_number"]{@value="' . $obj->external_number . '"}');
            $xml->update('/variables/variable[@name="internal_caller_id_name"]{@value="' . $obj->internal_name . '"}');
            $xml->update('/variables/variable[@name="internal_caller_id_number"]{@value="' . $obj->internal_number . '"}');
        }
    }

    public static function delete($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        $base = Bluebox_Record::getBaseTransactionObject();

        if ($base instanceof Conference) {
            FreeSwitch::section('conference_profile', $base->conference_id);

            $xml->deleteNode('/param[@name="caller-id-name"]');
            $xml->deleteNode('/param[@name="caller-id-number"]');
        } elseif ($base instanceof Device) {
            if ($base->User->Location->domain == '') {
                $domain = '$${domain}';
            } else {
                $domain = '$${location_' . $base->User->location_id . '}';
            }
            FreeSwitch::setSection('user', $domain, $base->Sip->username);

            $xml->deleteNode('/variables/variable[@name="outbound_caller_id_name"][@value="' . $obj->external_name . '"]');
            $xml->deleteNode('/variables/variable[@name="outbound_caller_id_number"][@value="' . $obj->external_number . '"]');
            $xml->deleteNode('/variables/variable[@name="internal_caller_id_name"][@value="' . $obj->internal_name . '"]');
            $xml->deleteNode('/variables/variable[@name="internal_caller_id_number"][@value="' . $obj->internal_number . '"]');
        }
    }

    public static function conditioning()
    {
        Kohana::log('debug', 'Adding Caller ID conditioning section');
        
        // Reference to our XML document, positioned at the right extension for us :-)
        $xml = FreeSWITCH::createExtension('callerid');
        $condition = '/condition[@field="${internal_caller_id_number}"][@expression="^.+$"]';
        $xml->update($condition . '/action[@application="set"][@data="effective_caller_id_name=${internal_caller_id_name}"]');
        $xml->update($condition .'/action[@application="set"][@data="effective_caller_id_number=${internal_caller_id_number}"]');

    }
}
