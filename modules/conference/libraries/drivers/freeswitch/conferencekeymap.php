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
 * Karl Anderson
 *
 */

/**
 * conferencenumber.php - FreeSwitch Conference Number driver
 * 
 * Allows for a number to have a conference bridge as a final destination in the dialplan. Links it to the
 * conference FreeSWITCH XML configuration files.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage CallerId
 */
class FreeSwitch_ConferenceControls_Driver extends FreeSwitch_Base_Driver {
    private $actions = array(
        ACTION_MUTE => 'mute',
        ACTION_GAIN_DOWN => 'vol talk dn',
        ACTION_GAIN_RESET => 'vol talk zero',
        ACTION_GAIN_UP => 'vol talk up',
        ACTION_VOLUME_DOWN => 'vol listen dn',
        ACTION_VOLUME_RESET => 'vol listen zero',
        ACTION_VOLUME_UP => 'vol listen up',
        ACTION_NOISELEVEL_DOWN => 'energy dn',
        ACTION_NOISELEVEL_RESET => 'energy equ',
        ACTION_NOISELEVEL_UP => 'energy up',
        ACTION_DEAF => 'deaf mute',
        ACTION_HANGUP => 'hangup',
        ACTION_TRANSFER => 'transfer'
    );

    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        $prefix = sprintf('//document/section[@name="configuration"]/configuration[@name="conference.conf"]/caller-controls/group[@name="controls_%s"]', $obj->conference_controls_id);

        foreach ($obj->ConferenceKeymap as $keymap) {
            $action = $this->actions[$keymap->action];

            if ($action == 'transfer') {
                $extra = '{data="' . dialplan::transfer($keymap->number_id) . '"}';
            } else
                $extra = '';

            // TODO: Fixme. Should use the dialplan helper to generate transfers and key actions
            $xml->update($prefix . '/control[@digits="' . $keymap->key . '"]{@action="' . $keymap->action . '"}' . $extra);
        }
    }
}
