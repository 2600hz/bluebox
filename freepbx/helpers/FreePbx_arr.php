<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * FreePbx_arr.php - FreePbx array helper extension
 *
 * @author Karl Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage Core
 */

class arr extends arr_Core {

    public static function update(&$array, $key, $value)
    {
        if (!isset($array[$key])) {
            $array[$key] = $value;
        } else if(is_array($array[$key])) {
            $array[$key][] = $value;
        } else if(is_string($array[$key])) {
            $array[$key] = $array[$key] . $value;
        } else {
            return FALSE;
        }
        return TRUE;
    }

    public static function array_compare_recursive($array1, $array2)
    {
        foreach ($array1 as $k1 => $v1) {
            if (!isset($array2[$k1])) {
                return FALSE;
            }
            $diff = array_diff($array2[$k1], $array1[$k1]);
            if(!empty($diff)){
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     *
     * This function will create the markup to convert
     * and array to a UL list
     *
     * @param array $array The array to convert
     * @param array $options The optional parameters to control the convertion
     * @param array $attributes The HTML params
     * @param string $keyName The name to use as the key
     * @param bool $_noUITag Part of the recursion to stop re-creation of UL tags
     * @return string HTML markup result
     */
    public static function arrayToUL($array, $options = array(), $attributes = NULL, $keyName = NULL, $_noUlTag = false)
    {
        // Sanity check, if there is no array or it is empty there is nothing to do....
        if (!is_array($array) || empty($array))
            return '';

        // This array will added any unset default options
        $options += array (
            'recursive' => true,
            'intKeys' => false,
            'strKeys' => true,
            'subKey' => false,
            'seperator' => ': '
        );

        // Set the options array as vars in this function
        extract($options);

        // Create an empty string to append our elements to
        $list = '';


        foreach ($array as $key => $value)
        {
            // If the value is a sub array and we are allow to recurse
            if (is_array($value) && $recursive)
            {
                // Determine what the lable for this value should be
                if ($subKey && $strKeys && is_string($key))
                {
                   $subKeyName = $key;
                } else if ($subKey && $intKeys && is_int($key)) {
                    $subKeyName = $key;
                } else if (!empty($keyName)) {
                    $subKeyName = $keyName;
                } else {
                    $subKeyName = '';
                }

                // Recurse into the element
                $list .= self::arrayToUL($value, $options, $attributes, $subKeyName, true);

            // If the value is not empty and not an array
            } else if (!empty($value) && !is_array($value)) {

                // Add the value to the markup using the options to figure out what it should be labeled as
                if (!empty($keyName))
                {
                    $list .= '<li>' .$keyName .$seperator .$value .'</li>';
                } else if ($strKeys && is_string($key)) {
                    $list .= '<li>' .$key .$seperator .$value .'</li>';
                } else if ($intKeys && is_int($key)) {
                    $list .= '<li>' .$key .$seperator .$value .'</li>';
                } else {
                    $list .= '<li>' .$value .'</li>';
                }
            }
        }

        // If this was the parent wrap the list items in ul tags with any html attributes supplied
        if (!$_noUlTag)
            return '<ul' .(is_array($attributes) ? ' ' .html::attributes($attributes) : '') .'>' .$list .'</ul>';
        else
            // If this was the result of recursion just return the list string
            return $list;
    }

    public static function parse_str($string, &$array) {
        $keyName = str_replace('[]', '', $string);
        parse_str($keyName, $keyName);
        $arrayPt = &$array;
        do {
            $key = key($keyName);
            if (isset($arrayPt[$key])) {
                $arrayPt = &$arrayPt[$key];
            } else {
                return NULL;
            }
        } while ($keyName = array_shift($keyName));
        return $arrayPt;
    }

    function flatten($array) {
        $result = array();
        $stack = array();
        array_push($stack, array("", $array));

        while (count($stack) > 0) {
            list($prefix, $array) = array_pop($stack);

            foreach ($array as $key => $value) {
                if (empty($prefix)) {
                    $new_key = $prefix . strval($key);
                } else {
                    $new_key = $prefix . '[' . strval($key) .']';
                }

                if (is_array($value))
                    array_push($stack, array($new_key, $value));
                else
                    $result[$new_key] = $value;
            }
        }

        return $result;
    }

    public static function array_merge_recursive_distinct ( array &$array1, array &$array2 )
    {
      $merged = $array1;

      foreach ( $array2 as $key => &$value )
      {
        if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
        {
          $merged [$key] = self::array_merge_recursive_distinct ( $merged [$key], $value );
        }
        else
        {
          $merged [$key] = $value;
        }
      }

      return $merged;
    }
}
