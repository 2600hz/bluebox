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
 * conference.php - Conference creation, management and administration controller.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Conference
 */

class Conference extends Bluebox_Record {
    /**
     * Silence music on hold, even when nobody is in the conference
     */
    const MOH_NONE = 0;

    /**
     * Inherit music on hold from the active channel, or global setting
     */
    const MOH_INHERIT = 1;

    /**
     * Use a specific MOH stream for this conference
     */
    const MOH_SPECIFIC = 2;

    public static $errors = array(
        'name' => array(
            'required' => 'Name is required.'
        )
    );

    function setTableDefinition(){
        $this->hasColumn('conference_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('room_pin', 'string', 20, array('default' => NULL));
        $this->hasColumn('record', 'boolean', NULL, array('default' => false));
        $this->hasColumn('record_location', 'string', 100);
        $this->hasColumn('comfort_noise', 'boolean', NULL, array('notnull' => true, 'default' => true));
        $this->hasColumn('moh_type', 'integer', 11, array('notnull' => true, 'default' => 1));
        $this->hasColumn('moh_file', 'string', 100);
        $this->hasColumn('conference_soundmap_id', 'integer', 11, array('unsigned' => true, 'default' => 1));
    }

    function setUp() {
        $this->hasOne('ConferenceNumber as Number', array('local' => 'conference_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));
        $this->hasOne('ConferenceSoundmap', array('local' => 'conference_soundmap_id', 'foreign' => 'conference_soundmap_id'));
        $this->hasMany('ConferencePins', array('local' => 'conference_id', 'foreign' => 'conference_id'));

        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}
