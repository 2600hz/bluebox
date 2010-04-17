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
 * Module.php - Track what modules are installed and working
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

class Module extends FreePbx_Record
{
    /**
     * Sets the table name, and defines table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('module_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('display_name', 'string', 100, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('module_version', 'float', 11, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('enabled', 'boolean', null, array('notnull' => true, 'default' => true));
        $this->hasColumn('basedir', 'string', 255, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('parameters', 'array');
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
	public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('Number', array('local' => 'module_id', 'foreign' => 'module_id'));  // A module may have a bunch of numbers tied to it
        $this->hasMany('ModuleUser', array('local' => 'module_id', 'foreign' => 'module_id'));    // A user may have a bunch of modules enabled/disabled

        // BEHAVIORS
        $this->actAs('Timestampable');
        $this->actAs('Versionable');
    }
}
