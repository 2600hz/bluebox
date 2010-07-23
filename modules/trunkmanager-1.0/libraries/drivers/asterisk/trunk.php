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
 * trunk.php - Asterisk Trunk configuration driver
 *
 * Allows for configuration of inbound and outbound trunks
 *
 * @author Karl Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage Trunk
 */
class Asterisk_Trunk_Driver extends Asterisk_Base_Driver {
    /**
     * Indicate we support FreeSWITCH
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

        // Get a unqiue name for this trunk
        $trunkName = 'trunk_' . $base->trunk_id;

        // If this is a sip trunk it belongs in sip.conf
        if (isset($base['plugins']['sip']['username'])) {
            $ami->queueConfigUpdate('sip.conf', 'NewCat', $trunkName);
            $ami->queueConfigUpdate('sip.conf', 'Append', $trunkName, 'type', 'friend');
            $ami->queueConfigUpdate('sip.conf', 'Append', $trunkName, 'host', $obj->server);
            $ami->queueConfigUpdate('sip.conf', 'Append', $trunkName, 'canreinvite', 'no');
            $ami->queueConfigUpdate('sip.conf', 'Append', $trunkName, 'context', 'default');
        }
    }

    public static function delete($obj)
    {

    }
}
