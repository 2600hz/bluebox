<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Bluebox/Libraries/Validation
 * @author     Darren Schreiber
 * @license    Mozilla Public License (MPL)
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
        {
            return $errors[$fieldName];
        }
        else
        {
            return '';
        }
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
        if (!empty($translate))
        {
            $value = __($value);
        }

        return parent::add_error($field, $value);
    }
}
