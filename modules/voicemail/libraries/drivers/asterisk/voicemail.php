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
 * voicemailtoemail.php - Asterisk VoiceMailToEmail driver
 * Adds VoiceMailToEmail variables to Asterisk configuration files.
 *
 * @author Karl Anderson
 * @package Bluebox
 * @subpackage VoicemailToEmail
 * @license MPL
 */
class Asterisk_Voicemail_Driver extends Asterisk_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // If there is no mailbox name then exit, dont know if this is necessary
        if (empty($obj->mailbox))
            return false;

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
            $location = 'default';
        } else {
            $location = 'location_' . $base->User->location_id;
        }

        // Set up so basic defaults for VM
        $mailbox['password'] = $obj->password;
        $mailbox['user_name'] = $base->User->first_name . ' ' . $base->User->last_name;
        $mailbox['email_address'] = '';
        $mailbox['pager_email_address'] = '';
        $mailbox['options'] = array('dialout' => 'dialout=' .$location, 'callback' => 'callback=' .$location);

        // If the user has provided an email then also setup any additional settings
        if (!empty($obj->email_all_messages))
        {
            $mailbox['email_address'] = $obj->email_address;

            if (!empty($obj->attach_audio_file))
            {
               $mailbox['options']['attach'] = 'attach=yes';
            } else {
               $mailbox['options']['attach'] = 'attach=no';
            }

            if (!empty($obj->delete_file))
            {
               $mailbox['options']['delete'] = 'delete=yes';
            } else {
               $mailbox['options']['delete'] = 'delete=no';
            }
        }

        // The mailbox options need to be seperated by a pipe
        $mailbox['options'] = implode('|', $mailbox['options']);

        // First add the VM to the sip.conf for MWI and such
        $ami->queueConfigUpdate('sip.conf', 'NewCat', $username);
        $ami->queueConfigUpdate('sip.conf', 'Append', $username, 'mailbox', $obj->mailbox . '@' . $location);

        // Now add the entry to voicemail.conf
        $ami->queueConfigUpdate('voicemail.conf', 'NewCat', $location);
        $ami->queueConfigUpdate('voicemail.conf', 'Append', $location, $obj->mailbox, '>' .implode(',', $mailbox));
    }

    public static function delete($obj)
    {

    }

    public static function prenumber() {
        
    }

    public static function postnumber() {

    }

}
