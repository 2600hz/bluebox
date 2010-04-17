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
 * voicemailtoemail.php - FreeSwitch VoiceMailToEmail driver
 * Adds VoiceMailToEmail variables to FreeSWITCH XML configuration files.
 *
 * @author Michael Phillips
 * @package FreePBX3
 * @subpackage VoicemailToEmail
 * @license MPL
 */
class FreeSwitch_Voicemail_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        $base = FreePbx_Record::getBaseTransactionObject();

        $xml = FreeSwitch::setSection('user', 'voicemail_' . $obj->account_id, $obj->mailbox);
        $xml->update('/params/param[@name="sip-forbid-register"]{@value="true"}');
        $xml->update('/params/param[@name="vm-password"]{@value="' . $obj->password . '"}');
        $xml->update('/params/param[@name="vm-mailto"]{@value="' . $obj->email_address . '"}');
        $xml->update('/params/param[@name="email-addr"]{@value="' . $obj->email_address . '"}');
        $xml->update('/params/param[@name="vm-delete-file"]{@value="' . ($obj->delete_file ? 'true' : 'false') . '"}');
        $xml->update('/params/param[@name="vm-attach-file"]{@value="' . ($obj->attach_audio_file ? 'true' : 'false') . '"}');
        $xml->update('/params/param[@name="vm-email-all-messages"]{@value="' . ($obj->email_all_messages ? 'true' : 'false') . '"}');
        //$xml->update($prefix . '/params/param[@name="storage-dir"]{@value="' . $voicemail->getStorageDir() . '"}');
        //$xml->update($prefix . '/params/param[@name="vm-alternate-greet-id"]{@value="' . $voicemail->getGreetings() . '"}');

        if ($base instanceof Device) {
            if (!empty($obj->foreign_id)) {
                // point the user the users MWI to our mailbox
                $xml = FreeSwitch::setSection('user', '$${location_' . $base->User->location_id . '}', $base->Sip->username);
                $xml->update('/params/param[@name="mwi-account"]{@value="' . $obj->mailbox .'@voicemail_' . $obj->account_id . '"}');
            } else {
                $xml = FreeSwitch::setSection('user', '$${location_' . $base->User->location_id . '}', $base->Sip->username);
                $xml->deleteNode('/params/param[@name="mwi-account"]');
            }
        }
    }

    public static function delete($obj)
    {
        $base = FreePbx_Record::getBaseTransactionObject();

        $xml = FreeSwitch::setSection('user', 'voicemail_' . $obj->account_id, $obj->mailbox);
        $xml->deleteNode();

  	if ($base instanceof Device) {            
            $xml = FreeSwitch::setSection('user', '$${location_' . $base->User->location_id . '}', $base->Sip->username);
            $xml->update('/params/param[@name="mwi-account"]{@value=""}');
        }
    }

    public static function dialplan($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $domain = 'voicemail_' .$obj->Voicemail->account_id;
        $mailbox = $obj->Voicemail->mailbox;

        $xml->update('/action[@application="answer"]');
        $xml->update('/action[@application="sleep"]{@data="1000"}');
        $xml->update('/action[@application="voicemail"]{@data="default ' . $domain . ' ' . $mailbox . '"}');
    }

    public static function preNumber()
    {
        $xml = Telephony::getDriver()->xml;

        // TODO: Fix this.
        Doctrine::initializeModels('DeviceVoicemail');
        Doctrine::initializeModels('UserVoicemail');

        $obj = Event::$data;
        if (($obj instanceof DeviceNumber) and ($obj->Device->Voicemail)) {
            $base = $obj->Device->Voicemail;
            $domain = 'voicemail_' . $obj->Device->Voicemail->account_id;
            $mailbox = $obj->Device->Voicemail->mailbox;
        } elseif (($obj instanceof Device) and ($obj->Device->Voicemail)) {
            $base = $obj->Voicemail;
            $domain = 'voicemail_' . $obj->Device->Voicemail->account_id;
            $mailbox = $obj->Voicemail->mailbox;
        }

        if (!empty($base->foreign_id) && !empty($domain) && !empty($mailbox)) {
            $xml->update('/action[@application="set"][@freepbx="settingEndBridge"][@data="hangup_after_bridge=true"]');
            $xml->update('/action[@application="set"][@freepbx="settingTimeout"]{@data="call_timeout=' . 30 . '"}');
            $xml->update('/action[@application="set"][@freepbx="settingFail"]{@data="continue_on_fail=true"}');
        }
    }

    public static function postNumber()
    {
        $xml = Telephony::getDriver()->xml;

        $obj = Event::$data;
        if (($obj instanceof DeviceNumber) and ($obj->Device->Voicemail)) {
            $base = $obj->Device->Voicemail;
            $domain = 'voicemail_' .$obj->Device->Voicemail->account_id;
            $mailbox = $obj->Device->Voicemail->mailbox;
        } elseif (($obj instanceof Device) and ($obj->Device->Voicemail)) {
            $base = $obj->Voicemail;
            $domain = 'voicemail_' .$obj->Device->Voicemail->account_id;
            $mailbox = $obj->Voicemail->mailbox;
        }

        if (!empty($base->foreign_id) && !empty($domain) && !empty($mailbox)) {
            $xml->update('/action[@application="answer"]');
            $xml->update('/action[@application="voicemail"]{@data="default ' . $domain . ' ' . $mailbox . '"}');
        }
    }
}
