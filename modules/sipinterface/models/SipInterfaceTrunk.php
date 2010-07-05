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
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
 * 
 * Contributor(s):
 * 
 */

/**
 * SipInterfaceTrunk.php - SipInterfaceTrunk class
 * Created on Aug 21, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage SipInterface
 */
class SipInterfaceTrunk extends Bluebox_Record {
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('sipinterface_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true));
		$this->hasColumn('trunk_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true));
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('SipInterface', array('local' => 'sipinterface_id', 'foreign' => 'sipinterface_id', 'onDelete' => 'CASCADE'));
        $this->hasOne('Trunk', array('local' => 'trunk_id', 'foreign' => 'trunk_id'));
        $this->hasOne('SipTrunk', array('local' => 'trunk_id', 'foreign' => 'trunk_id'));

        // TODO: Fix this. Should not be dependent on Sip or Trunk modules frankly
        $relateTable = Doctrine::getTable('SipTrunk');
        $relateTable->bind(array('SipInterfaceTrunk', array('local' => 'trunk_id', 'foreign' => 'trunk_id')), Doctrine_Relation::ONE);
        $relateTable = Doctrine::getTable('Trunk');
        $relateTable->bind(array('SipInterfaceTrunk', array('local' => 'trunk_id', 'foreign' => 'trunk_id')), Doctrine_Relation::ONE);

        // BEHAVIORS
        $this->actAs('Timestampable');
	}
}
