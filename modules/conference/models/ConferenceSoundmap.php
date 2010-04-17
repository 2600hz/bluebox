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
 * ConferenceSoundmap.php - Conference sound mapping model.
 *
 * This model stores a list of sound files to be used for conferences (such as enter/exit/etc. sounds)
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Conference
 */

class ConferenceSoundmap extends FreePbx_Record {
    function setTableDefinition() {
        $this->hasColumn('conference_soundmap_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true));
        $this->hasColumn('mute', 'string', 100);
        $this->hasColumn('unmute', 'string', 100);
        $this->hasColumn('onlymember', 'string', 100);
        $this->hasColumn('join', 'string', 100);
        $this->hasColumn('exit', 'string', 100);
        $this->hasColumn('kicked', 'string', 100);
        $this->hasColumn('locked', 'string', 100);
        $this->hasColumn('unlocked', 'string', 100);
        $this->hasColumn('reject_locked', 'string', 100);
        $this->hasColumn('askpin', 'string', 100);
        $this->hasColumn('badpin', 'string', 100);
        $this->hasColumn('background', 'string', 100);
    }

    function setUp() {
        $this->hasOne('Conference', array('local' => 'conference_soundmap_id', 'foreign' => 'conference_soundmap_id'));

        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
