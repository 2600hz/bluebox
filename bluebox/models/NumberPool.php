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
 * NumberPool - Maps numbers to a pool of related numbers.
 *
 * The NumberPool class keeps track of what numbers are mapped to what number types. This is useful for auto-assignments
 * when a module needs a new number to assign a feature to.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */

class NumberPool extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('number_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true));
		$this->hasColumn('number_type_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true));
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Number', array('local' => 'number_id', 'foreign' => 'number_id'));
        $this->hasOne('NumberType', array('local' => 'number_type_id', 'foreign' => 'number_type_id'));
        
        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}
