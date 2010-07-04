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
 * CallerId.php - CallerId Plugin Model definition
 * Stores CallerId information that may be relevant to a number of other classes
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage CallerId
 */
class CallerId extends Bluebox_Record
{
    public static $errors = array(
        'internal_name' => array(
            'required' => 'Internal name is required.',
        ),
        'internal_number' => array(
            'required' => 'Internal number is required.',
            'regexp' => 'Enter numbers 0-9 only.',
        ),
        'external_name' => array(
            'required' => 'External name is required.',
        ),
        'external_number' => array(
            'required' => 'External number is required.',
            'regexp' => 'Enter numbers 0-9 only.',
        )    
    );
   
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
		$this->hasColumn('caller_id_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('internal_name', 'string', 40);
        $this->hasColumn('internal_number', 'string', 40, array('regexp' => '/^[\d]*$/'));
        $this->hasColumn('external_name', 'string', 40);
        $this->hasColumn('external_number', 'string', 40, array('regexp' => '/^[\d]*$/'));
	}

    /**
     * Sets up relationships, behaviors, etc.
     * Note that Caller Id can relate either to a user or a device, not both
     */
	public function setUp()
    {
        // RELATIONSHIPS

        // BEHAVIORS
        $this->actAs('Polymorphic');
        
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');  // Allows for saving of telephony configs each time this model is saved via events
	}
}
