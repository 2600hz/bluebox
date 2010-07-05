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
 * Trunk.php - Trunk model
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Trunk
 */

class Trunk extends Bluebox_Record {
    public static $errors = array(
    'name' => array(
    'required' => 'A name must be entered.',
    'default' => 'Invalid name.'
    ),
    'provider' => array(
    'required' => 'A provider must be entered.',
    'default' => 'Invalid provider.'
    ),
    'provider_id' => array(
    'required' => 'A provider must be entered',
    'default' => 'Invalid Provider'
    ),
    'server' => array(
    'required' => 'A server must be entered.',
    'default' => 'Invalid server.'
    )
    );

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition() {
    // COLUMN DEFINITIONS
        $this->hasColumn('trunk_id', 'integer', 11, array(
            'unsigned' => true,
            'notnull' => true,
            'primary' => true,
            'autoincrement' => true
            )
        );

        $this->hasColumn('name', 'string', 100, array(
            'notnull' => true,
            'notblank' => true
            )
        );

        $this->hasColumn('active', 'boolean', 1, array(
            'default' => 1
            )
        );
        $this->hasColumn('server', 'string', 100);
        
        $this->hasColumn('context_id', 'integer', 11, array(
                'unsigned' => true
            )
        );
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp() {
        // RELATIONSHIPS

        // BEHAVIORS
        $this->actAs('Polymorphic');

        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}
