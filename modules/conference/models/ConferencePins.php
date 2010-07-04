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
 * ConferencePins.php - Conference pins.
 *
 * This model tracks all pins available for a conference. This is useful to allow members to each have their own pins,
 * or to have multiple moderator pins, etc. Each pin also gets an associated list of controls assigned to it.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Conference
 */

class ConferencePins extends Bluebox_Record {
    const TYPE_MEMBER = 0;
    const TYPE_MODERATOR = 1;

	function setTableDefinition(){
		$this->hasColumn('conference_pin_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('conference_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('name', 'string', 100);
        $this->hasColumn('pin', 'integer', 20, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('type', 'integer', 11, array('notnull' => true));
        $this->hasColumn('conference_controls_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 0));
	}

	function setUp() {
        $this->hasOne('Conference', array('local' => 'conference_id', 'foreign' => 'conference_id'));
        $this->hasOne('ConferenceControls', array('local' => 'conference_controls_id', 'foreign' => 'conference_controls_id'));
        
        $this->actAs('Timestampable');
	}
}
