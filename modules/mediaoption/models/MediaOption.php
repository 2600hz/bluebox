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
 * Sip.php - SIP configuration model for storing SIP related settings
 * 
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage MediaOption
 */
class MediaOption extends FreePbx_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
	$this->hasColumn('media_option_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('class_type', 'string', 40);
	$this->hasColumn('foreign_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('media_workaround', 'integer', 11, array('unsigned' => true, 'default' => 0));
        $this->hasColumn('preferred_codec', 'integer', 11, array('unsigned' => true, 'default' => 0));
        $this->hasColumn('allowed_codecs', 'array', NULL);
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
	public function setUp()
    {
        // RELATIONSHIPS

        // BEHAVIORS
        $this->actAs('Polymorphic');
        
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
	}
}
