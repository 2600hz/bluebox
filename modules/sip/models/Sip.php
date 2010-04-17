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
 * Sip.php - SIP configuration model for storing SIP related settings
 * 
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Sip
 */
class Sip extends FreePbx_Record
{
    public static $errors = array(
        'username' => array (
            'required' => 'Username is required.',
            'length' => 'Username not long enough.'
        ),
        'password' => array (
            'required' => 'Password is required.',
            'length' => 'Password not long enough.'
        )
    );

    /**
     * Sets the table name, and defines the table columns.
     */
	function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('sip_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('username', 'string', 64);
        $this->hasColumn('password', 'string', 64);
        $this->hasColumn('cid_format', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'unsigned' => true, 'default' => 0));
        $this->hasColumn('sip_invite_format', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 0));
        $this->hasColumn('from_user', 'string', 64);
        $this->hasColumn('from_domain', 'string', 64);
        $this->hasColumn('contact', 'string', 64, array('default' => ''));
        $this->hasColumn('mac_address', 'string', 12);
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
	function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('SipConfiguration', array('local' => 'sip_id', 'foreign' => 'sip_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }

    public function postValidate($event) {
        $invoker =& $event->getInvoker();
        $settings = $invoker->toArray();                  

        $base = FreePbx_Record::getBaseTransactionObject();

        // Sip Username is required on a device. At least for FS.
        if ($base instanceof Device) {
            $errorStack = $this->getErrorStack();
            if(empty($settings['username'])) {     
                $errorStack->add('username', 'required');
            }
        }
     }

}
