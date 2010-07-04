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
 * RingGroup.php - RingGroup creation, management and administration controller.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage RingGroup
 * @TODO remove numbers from groups that are deleted
 */

class RingGroup extends Bluebox_Record {
    const STRATEGY_SEQUENTIAL = 0;
    const STRATEGY_SIMULTANEOUS = 1;
    const STRATEGY_ROUNDROBIN = 2;
    const STRATEGY_MOSTIDLE = 3;

    function setTableDefinition(){
        $this->hasColumn('ring_group_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('strategy', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('timeout', 'integer', 4, array('unsigned' => true));
        $this->hasColumn('fallback_number_id', 'integer', 11, array('unsigned' => true));
    }

    function setUp() {
        $this->hasOne('RingGroupNumber as Number', array('local' => 'ring_group_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));
        $this->hasOne('Number as FallbackNumber', array('local' => 'fallback_number_id', 'foreign' => 'number_id'));
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id'));
        $this->hasMany('RingGroupMember', array('local' => 'ring_group_id', 'foreign' => 'ring_group_id', 'onDelete' => 'CASCADE'));

        $this->actAs('Timestampable');
		$this->actAs('TelephonyEnabled');
    }
}
