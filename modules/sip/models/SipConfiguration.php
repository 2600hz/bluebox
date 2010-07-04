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
 * User.php doctrine record
 * @author bluebox team
 * @license MPL
 * @package Bluebox
 * @subpackage Sip
 *
 */

class SipConfiguration extends Bluebox_Record {
	function setTableDefinition(){
		$this->hasColumn('sip_configuration_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('sip_id', 'integer', 11, array('unsigned' => true));
		$this->hasColumn('key', 'string', 255); // key is a reserved word
		$this->hasColumn('value', 'string', 255); // value is a reserved word
		$this->hasColumn('category', 'string', 255); //category, reserved word
	}

	function setUp() {
        /*
         * Relationships
         */
		$this->hasOne('Sip', array('local' => 'sip_id', 'foreign' => 'sip_id'));

        /*
         * Behaviors
         */
        $this->actAs('Timestampable');
		$this->actAs('SoftDelete');
	}
}

?>
