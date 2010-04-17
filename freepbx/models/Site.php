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
 * Sites.php
 *
 * Keep track of URLs that can access this installation of FreePbx and settings related to how they should behave.
 * Examples include skins and access restrictions (access restrictions not yet implemented)
 *
 * @author Darren Schreiber
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
class Site extends FreePbx_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('site_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('url', 'string', 200, array('notnull' => true, 'notblank' => true, 'minlength' => 3));
        $this->hasColumn('homepage', 'string', 200, array('notnull' => true, 'default' => '/welcome'));
		$this->hasColumn('wildcard', 'boolean', array('notnull' => true, 'default' => true));
		$this->hasColumn('skin_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('default', 'boolean', array('notnull' => true, 'default' => false));
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	public function setUp()
    {
        // RELATIONSHIPS
		$this->hasOne('Skin', array('local' => 'skin_id', 'foreign' => 'skin_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
	}
}

