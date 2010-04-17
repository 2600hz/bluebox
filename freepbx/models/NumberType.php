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
 * NumberType.php - Track what number types are installed and working
 *
 * Number types are classes that can act as termination points for the numbers table. i.e. they generate "final" dialplan entries,
 * like connecting a caller to a conference bridge or a group of users.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

class NumberType extends FreePbx_Record
{
    const TYPE_NORMAL = 0;  // Normal implies numbers of this type can be used as part of any routing method (groups, system, etc.)
    const TYPE_NOGROUP = 1; // Nogroup implies numbers of this tyep can not be a destination in a group. Examples include groups themselves, conferences, etc.

    /**
     * Sets the table name, and defines table columns.
     */
	public function setTableDefinition()
    {
        // Don't add foreign key constraints because core number types will have no module association, and that's OK :-) We only use the module_id on install/remove
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

        // COLUMN DEFINITIONS
		$this->hasColumn('number_type_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('module_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true));
		$this->hasColumn('class', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'integer', 11, array('default' => NumberType::TYPE_NORMAL));
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Module', array('local' => 'module_id', 'foreign' => 'module_id'));    // A number endpoint must belong to a module

        // BEHAVIORS
        $this->actAs('Timestampable');
	}
}

