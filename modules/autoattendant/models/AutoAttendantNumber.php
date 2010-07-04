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
 * AutoAttendantNumber.php - Number reservation model for Auto Attendants
 *
 * This model is an alias for Numbers. It allows for numbers to be reserved for auto attendants specifically (without requiring a user)
 * from the same allocation of numbers in the general numbers table.
 *
 * Created on Jun 10, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
class AutoAttendantNumber extends Number {
    public static $description = 'AutoAttendant';

    public function initialize()
    {
        $numberType = new NumberType();
        $numberType->class = 'AutoAttendantNumber';
        $numberType->module_id = 0;
        $numberType->save();
    }

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

        /*
         * Relationships
         */
        // Relate the auto attendant (auto_attendant_id) with a generic number identifier (foreign_id) in the Number class.
        // Note carefully that this only works because this model is related to Number
        // The Number class has some "magic" that auto relates the class
        $this->hasOne('AutoAttendant', array('local'   => 'foreign_id',
                                   'foreign' => 'auto_attendant_id'));

        // Add relation on the other side, too, including all extended models that may have already loaded
        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number')) {
                $numberTable = Doctrine::getTable($class);
                $numberTable->bind(array('AutoAttendant', array('local' => 'foreign_id', 'foreign' => 'auto_attendant_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}
