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
 * tts.php - tts class
 * Created on Jul 21, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Text_To_Speech
 */
class FreeSwitch_Tts_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        if (get_class($obj) == 'Conference') {
            // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
            $prefix = sprintf('//document/section[@name="configuration"]/configuration[@name="conference.conf"]/profiles/profile[@name="conference_%s"]', $obj->conference_id);

            //$xml->update($prefix . '/param[@name="tts-engine"]{@value="cepstral"}');
            //$xml->update($prefix . '/param[@name="tts-voice"]{@value="david"}');
        }
    }

    public static function dialplan($obj)
    {
        
    }

}
