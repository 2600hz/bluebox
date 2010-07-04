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
 * address.php - Address Management Plugin Model
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Address
 */

class Address extends Bluebox_Record
{
    public static $errors = array(
        'address' => array (
            'required' => 'Address is required.',
            'alpha' => 'Only letters are allowed.',
            'length' => 'Must be 3+ characters.',
            'default' => 'Invalid Street Address.',
        ),
        'zip' => array (
                'required' => 'Zip code is required.',
                'numeric' => 'Only numbers are allowed.',
                'default' => 'Invalid Zip Code.',
        ),
        'city' => array (
                'required' => 'City is required.',
                'default' => 'Invalid city.',
        ),
        'state' => array (
                'required' => 'State code is required.',
                'default' => 'Invalid state.',
        ),
    );

    /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('address_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('address', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('city', 'string', 64, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('state', 'string', 10, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('zip', 'string', 12, array('notnull' => true, 'notblank' => true));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    function setUp()
    {
        // RELATIONSHIPS

        // Add relation on the other side, too, including all extended models that may have already loaded
        $this->actAs('Polymorphic');

        // BEHAVIORS
        $this->actAs('Timestampable');
        //$this->actAs('Versionable');
    }
}

