<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Html
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class html extends html_Core
{
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
    public static function image($src = NULL, $alt = NULL, $index = FALSE)
    {
        // Create attribute list
        $attributes = is_array($src) ? $src : array('src' => $src);

        if ( ! empty($attributes['module']) )
        {
            $m = glob(MODPATH .$attributes['module'] .'*');
            
            if ( count($m) > 0 )
            {
                $attributes['src']  = 'modules' .DIRECTORY_SEPARATOR;
                
                $attributes['src'] .= basename($m[0]) .DIRECTORY_SEPARATOR;

                $attributes['src'] .= ltrim($attributes['src'], DIRECTORY_SEPARATOR);
            }
        }

        unset($attributes['module']);

        return parent::image($attributes, $alt, $index);
    }
}
