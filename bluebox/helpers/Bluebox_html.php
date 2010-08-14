<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Html
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
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

    /**
     * Creates a image link.
     *
     * If the $src is an array with a 'module' key, the function will try to
     * resolve the module's path and use the 'src' key as a relative path to
     * the found module's directory. Uses the first found module directory.
     *
     * array('module' => 'module-name', src => 'assets/img/file.png') =>
     *   'modules/module-name-vsn/assets/img/file.png'
     *
     * @param   string        image source, or an array of attributes
     * @param   string|array  image alt attribute, or an array of attributes
     * @param   boolean       include the index_page in the link
     * @return  string
     */
    public static function image($src = NULL, $alt = NULL, $index = FALSE) {
      // Create attribute list
      $attributes = is_array($src) ? $src : array('src' => $src);

      if ( ! empty($attributes['module']) ) {
	$m = glob(MODPATH . $attributes['module'] . '*');
	if ( count($m) > 0 ) {
	  $attributes['src'] = 'modules/' . basename($m[0]) . '/' . ltrim($attributes['src'], '/');
	}
      }

      unset($attributes['module']);

      return parent::image($attributes, $alt, $index);
    }
}
