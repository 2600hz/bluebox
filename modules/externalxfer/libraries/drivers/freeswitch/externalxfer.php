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
 * externalxfer.php - FreeSwitch ExternalXfer driver
 * Adds ExternalXfer variables to FreeSWITCH XML configuration files.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage ExternalXfer
 */
class FreeSwitch_ExternalXfer_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj) {

    }

    public static function delete($obj) {
        
    }

    public static function dialplan()
    {
        $xml = Telephony::getDriver()->xml;
        kohana::log('debug', 'Saving external destination to dialplan');
        $timeout = (isset($obj->options['timeout']) ? $obj->options['timeout'] : 30);

        if ($obj->ExternalXfer->route_type == 1) {
            $dialstring = 'sofia/';
        } else {
            $dialstring = 'sofia/';
        }

        $xml->update('/action[@application="set"][@bluebox="hangup_after_bridge"]{@data="hangup_after_bridge=true"}');
        $xml->update('/action[@application="set"][@bluebox="continue_on_fail"]{@data="continue_on_fail=true"}');
        $xml->update('/action[@application="set"][@bluebox="call_timeout"]{@data="call_timeout=' . $timeout . '"}');
        $xml->update('/action[@application="bridge"]{@data="{' . $dialstring . '}"}');

        // Nobody answered?
        // Grab the destination we're routing to. We don't care which context we end up in per say, as long as it's the right destination.
        // TODO: Handle contexts better?
        //if (!empty($obj->options['fallback_number_id']) and $obj->options['fallback_number_context']) {
        //    $context = $obj->RingGroup->FallbackNumber->NumberContext[0]->context_id;
        //    $xml->update('/action[@application="transfer"]{@data="' . $obj->RingGroup->FallbackNumber->number . ' XML context_' . $context. '"}');
        //}

    }
}
