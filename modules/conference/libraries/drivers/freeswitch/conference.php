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
class FreeSwitch_Conference_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot('//document/section[@name="configuration"]/configuration[@name="conference.conf"]/');

        // The section we are working with is <document><section name="configuration"><configuration name="conference.conf">
        $prefix = sprintf('/profiles/profile[@name="conference_%s"]', $obj->conference_id);

        // TODO: Codec needs to be moved to a codec plug-in
        //$xml->update($prefix . '/param[@name="rate"]{@value="8000' . $obj->XX . '"}');

        // TODO: Implement these
        $xml->update($prefix . '/param[@name="energy-level"]{@value="50"}');
        $xml->update($prefix . '/param[@name="caller-controls"]{@value="default"}');
        $xml->update($prefix . '/param[@name="member-flags"]{@value="waste"}');

        //$xml->update($prefix . '/param[@name="domain"]{@value="' . $obj->XX . '"}');

        //Base Directory for default sounds. TODO: Implement other languages
        //$xml->update($prefix . '/param[@name="sound-prefix"][@value="$${sounds_dir}/en/us/callie"]');
        $xml->update($prefix . '/param[@name="sound-prefix"][@value="' . filesystem::esc_dir(Kohana::config('freeswitch.cfg_root') . "/../sounds/en/us/callie") . '"]');

        // Sounds
        // (No idea what these two do...)
        //$xml->update($prefix . '/param[@name="ack-sound"]{@value="' . $obj->ConferenceSoundmap-> . '"}');
        //$xml->update($prefix . '/param[@name="nack-sound"]{@value="' . $obj->ConferenceSoundmap-> . '"}');
        $xml->update($prefix . '/param[@name="muted-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->mute) . '"}');
        $xml->update($prefix . '/param[@name="unmuted-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->unmute) . '"}');
        $xml->update($prefix . '/param[@name="alone-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->onlymember) . '"}');
        $xml->update($prefix . '/param[@name="perpetual-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->background) . '"}');

        switch ($obj->moh_type) {
            case 0:
                $xml->update($prefix . '/param[@name="moh-sound"]{@value=""}');
                break;
            case 2:
                $mohFile = FileManager::getFilePath($obj->moh_file);
                $xml->update($prefix . '/param[@name="moh-sound"]{@value="' . filesystem::esc_dir($mohFile) . '"}');
                break;
            default:
                $xml->update($prefix . '/param[@name="moh-sound"]{@value="$${hold_music}"}');
                break;
        }
        
        $xml->update($prefix . '/param[@name="enter-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->join) . '"}');
        $xml->update($prefix . '/param[@name="exit-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->exit) . '"}');
        $xml->update($prefix . '/param[@name="kicked-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->kicked) . '"}');
        $xml->update($prefix . '/param[@name="locked-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->locked) . '"}');
        $xml->update($prefix . '/param[@name="is-locked-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->reject_locked) . '"}');
        $xml->update($prefix . '/param[@name="pin-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->askpin) . '"}');
        $xml->update($prefix . '/param[@name="bad-pin-sound"]{@value="' . filesystem::esc_dir($obj->ConferenceSoundmap->badpin) . '"}');

        $xml->update($prefix . '/param[@name="comfort-noise"]{@value="' . $obj->comfort_noise . '"}');
        //$xml->update($prefix . '/param[@name="comfort-noise-level"]{@value="' . $obj->XX . '"}');
        $xml->update($prefix . '/param[@name="pin"]{@value="' . $obj->room_pin . '"}');

        if ($obj->record) {
            $recordLocation = rtrim($obj->record_location, '/') .'/' .'${conference_name}_${strftime(%Y-%m-%d-%H-%M-%S)}.wav';
            $xml->update($prefix . '/param[@name="auto-record"]{@value="' . filesystem::esc_dir($recordLocation) . '"}');
            //$xml->update($prefix . '/param[@name="auto-record"]{@value="' . $obj->record_location . '/${conference_name}_${strftime(%Y-%m-%d-%H-%M-%S)}.wav"}');
        } else {
            $xml->update($prefix . '/param[@name="auto-record"]{@value=""}');
        }
    }

    public static function delete($obj) {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot(sprintf('//document/section[@name="configuration"]/configuration[@name="conference.conf"]/profiles/profile[@name="conference_%s"]', $obj->conference_id));

        $xml->deleteNode();
    }

    public static function dialplan($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $xml->update('/action[@application="answer"]');
        $xml->update('/action[@application="sleep"]{@data="1000"}');    // Pause for a second on answer, to avoid audio clipping
        $xml->update('/action[@application="conference"]{@data="conference_' . $obj->Conference->conference_id . '@conference_' . $obj->Conference->conference_id . '"}');
    }
}
