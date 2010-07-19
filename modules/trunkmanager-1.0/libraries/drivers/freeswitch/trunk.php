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
 * trunk.php - FreeSwitch Trunk configuration driver
 *
 * Allows for configuration of inbound and outbound trunks
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Trunk
 */
class FreeSwitch_Trunk_Driver extends FreeSwitch_Base_Driver
{
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($trunk)
    {
        $plugins = $trunk['plugins'];

        if (!empty($plugins['sipinterface']['sipinterface_id']))
        {
            $interface = $plugins['sipinterface']['sipinterface_id'];

            $xml = FreeSwitch::setSection('gateway', 'sipinterface_' .$interface, 'trunk_' . $trunk['trunk_id']);

            $xml->update('/param[@name="realm"]{@value="' . $trunk['server'] . '"}');
        }

        $modified = $trunk->getModified(TRUE, TRUE);

        if (!empty($modified['plugins']['sipinterface']['sipinterface_id']))
        {
            $oldInterface = $modified['plugins']['sipinterface']['sipinterface_id'];

            if (empty($interface) OR $interface != $oldInterface)
            {
                $xml = FreeSwitch::setSection('trunk', 'sipinterface_' .$oldInterface, 'trunk_' . $trunk['trunk_id']);

                $xml->deleteNode();
            }

        }

        // Note - sip settings for trunks get added by the sip driver
    }

    public static function delete($trunk)
    {
        $plugins = $trunk['plugins'];

        if (!empty($plugins['sipinterface']['sipinterface_id']))
        {
            $xml = FreeSwitch::setSection('gateway', 'sipinterface_' .$plugins['sipinterface']['sipinterface_id'], 'trunk_' . $trunk['trunk_id']);

            $xml->deleteNode();
        }
    }
}