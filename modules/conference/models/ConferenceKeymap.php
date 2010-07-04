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
 * ConferenceKeymap.php - Conference key mappings.
 *
 * This model tracks caller control profiles for conference key mapping.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Conference
 */

class ConferenceKeymap extends Bluebox_Record {
    const ACTION_MUTE = 0;
    const ACTION_GAIN_DOWN = 1;
    const ACTION_GAIN_RESET = 2;
    const ACTION_GAIN_UP = 3;
    const ACTION_VOLUME_DOWN = 4;
    const ACTION_VOLUME_RESET = 5;
    const ACTION_VOLUME_UP = 6;
    const ACTION_NOISELEVEL_DOWN = 7;
    const ACTION_NOISELEVEL_RESET = 8;
    const ACTION_NOISELEVEL_UP = 9;
    const ACTION_DEAF = 10;
    const ACTION_HANGUP = 11;
    const ACTION_TRANSFER = 12;

	function setTableDefinition(){
		$this->hasColumn('conference_keymap_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('conference_controls_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('key', 'string', 10, array('notnull' => true));
        $this->hasColumn('action', 'integer', 11, array('notnull' => true));
        $this->hasColumn('number_id', 'integer', 100, array('unsigned' => true));  // Used if this is a transfer outside of the conf bridge
	}

	function setUp() {
        $this->hasOne('Number', array('local' => 'number_id', 'foreign' => 'number_id'));
        $this->hasOne('ConferenceControls', array('local' => 'conference_controls_id', 'foreign' => 'conference_controls_id'));
        
        $this->actAs('Timestampable');
	}
}
