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
 * RingGroupNumber.php - Number reservation model for ring groups
 *
 * This model is an alias for Numbers. It allows for numbers to be reserved for ring groups, but from the same allocation
 * of numbers from the Number table.
 *
 * Created on Jun 9, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage RingGroup
 */
class RingGroupNumber extends Number {
    public static $description = 'RingGroup';

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);

        /*
         * Relationships
         */
        // Relate the ring group (ringroup_id) with a generic number identifier (foreign_id) in the Number class.
        // Note carefully that this only works because this model is related to Number
        // The Number class has some "magic" that auto relates the class
        $this->hasOne('RingGroup', array('local'   => 'foreign_id',
                                          'foreign' => 'ring_group_id',
                                          'owningSide' => FALSE));

        $this->actAs('TelephonyEnabled');
    }
}
