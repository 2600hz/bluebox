<?php
defined('SYSPATH') or die('No direct access allowed.');
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
* K Anderson
*
*/
/**
 * errors.php - errors class
 * Created on Jun 1, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
class Validation extends Validation_Core
{
    /**
     *
     * @var Validation A validator object for validating form data. This is global to this controller and can be used by plugins.
     */
    public static $validator;

    /**
     * Instantiate a Kohana validation class
     *
     * @return void
     */
    public static function initialize($vars)
    {
        self::$validator = new Validation($vars);
    }

    /**
     * Determines and returns an error for a field
     *
     * @param string the name fo the field to check against
     * @return string
     */
    public static function getFieldError($fieldName)
    {
        $errors = $this->errors();
        if (isset($errors[$fieldName]))
            return $errors[$fieldName];
        else
            return '';
    }

    /**
     * This function overrides the kohana error method to seek error messages from doctrine models
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
    
    public function add_error($field, $value, $translate = TRUE)
    {
        if (!empty($translate)) {
            $value = __($value);
        }

        return parent::add_error($field, $value);
    }
}
