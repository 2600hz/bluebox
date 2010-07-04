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
 * odbc.php - FreeSwitch Odbc driver
 *
 * Odbc XML generation
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage Odbc
 */
class FreeSwitch_OdbcMap_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj)
    {
        // We know how to add ourselves to SipInterface, Lcr, CidLookup and Voicemail (for now)
        if (!$obj)
            return;

        // sanity check that we even have a valid odbc_id before we go mucking up the XML
        if (empty($obj->odbc_id))
            return;

        if ($obj->class_type == 'SipInterfaceOdbcMap') {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' . $obj->SipInterface->sipinterface_id);
            $prefix = '/settings';
        } elseif ($obj->class_type == 'LcrOdbcMap') {
            $xml = FreeSwitch::setSection('lcr');
            $prefix = '';
        } elseif ($obj->class_type == 'CidLookupOdbcMap') {
            $xml = FreeSwitch::setSection('cidlookup');
            $prefix = '';
        }

        $xml->update($prefix . '/param[@name="odbc-dsn"]{@value="$${odbc_' . $obj->odbc_id . '}"}');
    }

    public static function delete($obj)
    {
        if (!$obj)
            return;

        if ($obj->class_type == 'SipInterfaceOdbcMap') {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' . $obj->SipInterface->sipinterface_id);
            $prefix = '/settings';
        } elseif ($obj->class_type == 'LcrOdbcMap') {
            $xml = FreeSwitch::setSection('lcr');
            $prefix = '';
        } elseif ($obj->class_type == 'CidLookupOdbcMap') {
            $xml = FreeSwitch::setSection('cidlookup');
            $prefix = '';
        }

        $xml->deleteNode($prefix . '/param[@name="odbc-dsn"]');
    }
}
