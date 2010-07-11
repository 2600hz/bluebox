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
 *
 */

/**
 * SimpleRoute.php - SimpleRoute Plugin Model definition
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage SimpleRoute
 */

class SimpleRoute extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('simple_route_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('trunk_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('local', 'boolean', NULL, array('notnull' => TRUE, 'default' => FALSE));
        $this->hasColumn('local_prepend', 'string', 20, array('notnull' => TRUE, 'default' => ''));
        $this->hasColumn('international', 'boolean', NULL, array('notnull' => TRUE, 'default' => FALSE));
        $this->hasColumn('international_prepend', 'string', 20, array('notnull' => TRUE, 'default' => ''));
        $this->hasColumn('emergency', 'boolean', NULL, array('notnull' => TRUE, 'default' => FALSE));
        $this->hasColumn('emergency_prepend', 'string', 20, array('notnull' => TRUE, 'default' => ''));
        $this->hasColumn('area_code', 'integer', NULL, array());
        $this->hasColumn('caller_id_number', 'integer', NULL, array('default' => ''));
        $this->hasColumn('caller_id_name', 'string', 80, array('default' => 'Bluebox v3'));
    }

    /**
     * Sets up relationships, behaviors, etc.
     * Note that Caller Id can relate either to a user or a device, not both
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('SimpleRouteContext', array('local' => 'simple_route_id', 'foreign' => 'route_id', 'cascade' => array('delete')));

        // BEHAVIORS
        $this->actAs('TrunkRelation');
        //$this->actAs('TelephonyEnabled');  // Allows for saving of telephony configs each time this model is saved via events
    }
}
