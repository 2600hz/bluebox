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
 * Account.php - Account model
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

class Account extends FreePbx_Record
{
    const TYPE_DEMO = 1;
    const TYPE_NORMAL = 0;
    const TYPE_FREE = 2;
    const TYPE_BETA = 3;
    const TYPE_DISABLED = 4;
    const TYPE_SUPERADMIN = 5;

    public static $types = array(
        self::TYPE_DEMO => 'Demo/Trial',
        self::TYPE_NORMAL => 'Normal',
        self::TYPE_FREE => 'Free/Limited',
        self::TYPE_BETA => 'Beta Tester',
        self::TYPE_DISABLED => 'Disabled',
        self::TYPE_SUPERADMIN => 'System Admin'
    );

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 0));
        $this->hasColumn('expire', 'datetime', NULL);
        $this->hasColumn('description', 'string', 255);
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('Location', array('local' => 'account_id', 'foreign' => 'account_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}

