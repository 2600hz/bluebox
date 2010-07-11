<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Bluebox_html.php - Bluebox html helper extension
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Core
 */
class html extends html_Core {
    /**
     * @var bool When true the html helper methods echo the result instead of returning it
     */
    public static $inline = false;


    public static function br($data = NULL, $extra = '')
    {

        // Append or add the classes
        if (empty($data['class']))
        {
            $data['class'] = 'clr-left line-break';
        } else {
            $data['class'] = $data['class'] .' clr-left line-break';
        }

		$result = '<div'.html::attributes((array) $data).' '.$extra.'>&nbsp</div>';
        return self::_output($result);
    }

    /**
     * This is a simple wrapper to enforce the inline option, where if true the result of
     * any html helper is either returned or echo.
     *
     * @param string $result
     * @return string|void
     */
    private function _output($result = null)
    {
        // Execute any delayed methods
        if (!empty(self::$delayed_elements))
        {
            foreach (self::$delayed_elements as $method => $occurance)
            {
                foreach ($occurance as $number => $params)
                {
                    // First remove the delayed element so it does run in the recursion
                    // If it is still unsatisfied it will set its self back....
                    unset(self::$delayed_elements[$method][$number]);

                    try{
                        call_user_func_array(array('self', $method), $params);
                    } catch (Exception $e) { }
                }
            }
        }

        // If we should be displaying inline echo, otherwise return
        if (self::$inline)
            echo $result;
        else
            return $result;
    }
}
