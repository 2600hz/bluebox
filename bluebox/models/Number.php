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
 * Number.php - Track numbers (extensions, phone numbers, etc.) used in the system in one table with polymorphic relations
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */

class Number extends Bluebox_Record
{
    /**
     * Constants for number status.
     */
    const STATUS_NORMAL = 0;
    const STATUS_SYSTEM = 1;
    const STATUS_LOCKED = 2;

    public static $description = __CLASS__;
    
    public static $errors = array(
        'number' => array (
            'unique' => 'This number already exists',
            'required' => 'Number is required',
            'default' => 'Number must be letters and numbers only'
        )
    );
    /**
     * Sets the table name, and defines table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('number_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('number', 'string', 200, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('status', 'integer', 1, array('unsigned' => true, 'notnull' => true, 'default' => 0));     // 0 = none, 1 = locked
        $this->hasColumn('options', 'array');
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('NumberContext', array('local' => 'number_id', 'foreign' => 'number_id', 'cascade' => array('delete')));
        $this->hasMany('NumberPool', array('local' => 'number_id', 'foreign' => 'number_id', 'cascade' => array('delete')));
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id'));

        // BEHAVIORS
        $this->actAs('Polymorphic');
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}
