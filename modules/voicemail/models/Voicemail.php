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
 * Michael Phillips
 *
 * @license MPL
 * @package Bluebox
 * @subpackage VoicemailToEmail
 */
class Voicemail extends Bluebox_Record
{
    public static $errors = array(
        'mailbox' => array
        (
            'required' => 'Please provide mailbox number',
            'onlydigits' => 'Must be digits only'
        ),
        'password' => array
        (
            'required' => 'Please provide mailbox password',
            'onlydigits' => 'Must be digits only',
            'length' => 'Must be longer than 3 digits',
        ),
        'email_address' => array
        (
            'required' => 'Please provide an email address'
        )
    );
    
     /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('voicemail_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 64, array('notnull' => true));
        $this->hasColumn('mailbox', 'string', 64, array('notnull' => true));    // Mailbox number. NOT implied as this record can be tied to multiple devices/users
        $this->hasColumn('password', 'string', 64, array('notnull' => true));
        $this->hasColumn('email_address', 'string', 32);#, array('notnull' => false));
        $this->hasColumn('delete_file', 'boolean',1 , array('default' => false, 'notnull' => false));#, 1, array('default' => true));#, 'notnull' => true));
        $this->hasColumn('attach_audio_file', 'boolean',1 ,array('default' => false, 'notnull' => false));#, 1, array('default' => true));#, 'notnull' => true));
        $this->hasColumn('audio_format', 'string', 4);  // extension for voicemail messages i.e. mp3, wav, etc.
        $this->hasColumn('email_all_messages', 'boolean',1 ,array('default' => false, 'notnull' => false));#, 1, array('default' => true));#, 'notnull' => true));
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
    }

    public function setUp()
    {
        $this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id'));
        
        $this->actAs('Timestampable');
        $this->actAs('Polymorphic');
        $this->actAs('TelephonyEnabled');

        $this->actAs('MultiTenant');
    }

    public function postValidate($event)
    {
        $invoker =& $event->getInvoker();
        $settings = $invoker->toArray();

        if(!empty($settings['enabled'])) {
            $errorStack = $this->getErrorStack();

            // If the vm is enabled but we have no mailbox or it contains non-numbers error
            if(empty($settings['mailbox'])) {
                $errorStack->add('mailbox', 'required');
            } else if(!ctype_digit($settings['mailbox'])) {
                $errorStack->add('mailbox', 'onlydigits');
            }

            // If the vm is enabled but we dont have a pin, it contains non-numbers, or is not long enough error
            if(empty($settings['password'])) {
                $errorStack->add('password', 'required');
            } else if(!ctype_digit($settings['password'])) {
                $errorStack->add('password', 'onlydigits');
            } else if (strlen($settings['password']) < 4) {
                $errorStack->add('password', 'length');
            }

            // if the vm is enabled and we want to email messages but the email address is blank error out
            if (!empty($settings['email_all_messages']) && empty($settings['email_address'])) {
                $errorStack->add('email_address', 'required');
            }
        }
    }
   
}
