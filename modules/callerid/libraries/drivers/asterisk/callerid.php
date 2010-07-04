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
 * callerid.php - Asterisk CallerId driver
 * Adds CallerId variables to Asterisk sip.conf configuration file.
 *
 * @author Karl Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage CallerId
 */
class Asterisk_CallerId_Driver extends Asterisk_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // Get the asterisk driver
        $driver = Telephony::getDriver();

        // Get the asterisk manager
        $ami = $driver->ami;

        // Sanity check, ensure we have a connected manager
        if (!$ami->connected())
            return false;

        // Get the base of this configure object
        $base = Bluebox_Record::getBaseTransactionObject();

        // Get the username of this sip device
        $username = $base->Sip->username;

        // Get the domain of this user or set it to default
        if ($base->User->Location->domain == '') {
            $domain = 'default';
        } else {
            $domain = $base->User->Location->domain;
        }

        /* TODO: This is temporary to push everything into the default context */
        $domain = 'default';

        if ($base instanceof Conference) {
            /* TODO: I expect this will have to be handled in the dialplan */
        } elseif ($base instanceof Device) {
            $ami->queueConfigUpdate('sip.conf', 'Append', $username, 'callerid', '"' . $obj->internal_name .'" <' . $obj->internal_number . '>');
            $ami->queueConfigUpdate('sip.conf', 'Append', $username, 'setvar', 'external_cid = "' . $obj->external_name .'" <' . $obj->external_number . '>');
        }
    }

    public static function delete($obj)
    {

    }

    public static function conditioning()
    {

    }
}
