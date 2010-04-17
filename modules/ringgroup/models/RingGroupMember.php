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
 * RingGroup.php - RingGroup creation, management and administration controller.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage RingGroup
 */

class RingGroupMember extends FreePbx_Record {
	function setTableDefinition(){
		$this->hasColumn('ring_group_member_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('ring_group_id', 'integer', 11, array('unsigned' => true));
		$this->hasColumn('device_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('delay_ring', 'integer', 11, array('unsigned' => true));   // This is how long to delay BEFORE ringing this particular phone when the ring group is hit
	}

	function setUp() {
        $this->hasOne('RingGroup', array('local' => 'ring_group_id', 'foreign' => 'ring_group_id', 'onDelete' => 'CASCADE'));

        $this->actAs('Timestampable');
	}
}
