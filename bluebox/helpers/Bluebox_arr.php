<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Arr
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class arr extends arr_Core
{
    public static function find($array, $key, $value)
    {
        $filter = create_function('$array', "return ( \$array['$key'] == '$value');");

        return array_filter($array, $filter);
    }

    /**
     * This function will add the array key or overwrite it if it exists
     *
     * @param array The array possitioned at the root level of the key
     * @param mixed The array key to replace or update
     * @param mixed The value to place at the array key
     * @return bool
     */
    public static function update($array, $key, $value)
    {
        if (!isset($array[$key]))
        {
            $array[$key] = $value;
        } 
        else if(is_array($array[$key]))
        {
            $array[$key][] = $value;
        } 
        else if(is_string($array[$key]))
        {
            $array[$key] = $array[$key] . $value;   
        }
        
        return $array;
    }

    /**
     * This function will flatten a multidimensional array such that keys will
     * be concatened as string representation of their previsous array positions
     *
     * @param array The array to be be flattened
     * @param string The base key, usally the name of the array
     * @return array
     */
    public static function flatten($data, $cumlativeKey = '')
    {
        $result = array();

        foreach ($data as $key => $value)
        {
            if (!empty($cumlativeKey))
            {
                $key = $cumlativeKey .'[' .$key .']';
            }

            if (is_array($value))
            {
                $result = array_merge($result, self::flatten($value, $key));
            } 
            else
            {
                $result[$key] = $value;   
            }
        }

        return $result;
    }

    /**
     * Merges two arrays recursivly, in a disctinct manor
     *
     * @param array Array 1
     * @param array Array 2
     * @return array
     */
    public static function merge_recursive_distinct ( array &$array1, array &$array2 )
    {
        $merged = $array1;

        foreach ( $array2 as $key => &$value )
        {
            if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
            {
                $merged [$key] = self::merge_recursive_distinct ( $merged [$key], $value );
            } 
            else
            {
                $merged [$key] = $value;
            }
        }

        return $merged;
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
        {
            return '';
        }
        
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
                } 
                else if ($subKey && $intKeys && is_int($key))
                {
                    $subKeyName = $key;
                } 
                else if (!empty($keyName))
                {
                    $subKeyName = $keyName;
                } 
                else
                {
                    $subKeyName = '';
                }

                // Recurse into the element
                $list .= self::arrayToUL($value, $options, $attributes, $subKeyName, true);

            // If the value is not empty and not an array
            } 
            else if (!empty($value) && !is_array($value))
            {
                // Add the value to the markup using the options to figure out what it should be labeled as
                if (!empty($keyName))
                {
                    $list .= '<li>' .$keyName .$seperator .$value .'</li>';
                } 
                else if ($strKeys && is_string($key))
                {
                    $list .= '<li>' .$key .$seperator .$value .'</li>';
                } 
                else if ($intKeys && is_int($key))
                {
                    $list .= '<li>' .$key .$seperator .$value .'</li>';
                } 
                else
                {
                    $list .= '<li>' .$value .'</li>';
                }
            }
        }

        // If this was the parent wrap the list items in ul tags with any html attributes supplied
        if (!$_noUlTag)
        {
            return '<ul' .(is_array($attributes) ? ' ' .html::attributes($attributes) : '') .'>' .$list .'</ul>';
        }
        else
        {
            // If this was the result of recursion just return the list string
            return $list;
        }
    }


    public static function array_compare_recursive($array1, $array2)
    {
        foreach ($array1 as $k1 => $v1)
        {
            if (!isset($array2[$k1]))
            {
                return FALSE;
            }

            $diff = array_diff($array2[$k1], $array1[$k1]);

            if(!empty($diff))
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
}
