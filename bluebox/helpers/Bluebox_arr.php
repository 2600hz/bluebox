<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Arr
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class arr extends arr_Core
{
    public static $append_string_separator = ' ';

    public static function filter_collection($arrays, $paths, $value = NULL)
    {
        $paths = func_get_args();

        array_shift($paths);

        $value = array_pop($paths);

        $result = array();

        foreach ($arrays as $pos => $array)
        {
            if (self::get_array($array, $paths) == $value)
            {
                $result[$pos] = $array;
            }
        }

        return empty($result) ? FALSE : $result;
    }

    /**
     * This function determines if a var is iterable.
     * IE: safe for use in a foreach.
     *
     * Since this isnt specificly an array function it may be out of place
     *
     * @param mixed Var to test
     * @return bool
     */
    public static function is_iterable(&$mixed)
    {
        return isset($mixed) && (is_array($mixed) OR ($mixed instanceof Traversable));
    }

    public static function max_key($array)
    {
        if (empty($array))
        {
            return 0;
        }

        return max(array_keys($array));
    }

    /**
     * Gets the value deep inside $array using $path to find it.
     *
     * @author Terrence Howard <chemisus@gmail.com>
     * @param array $array
     * @param string $path ...
     * @return mixed
     */
    public static function get(&$array, $path)
    {
        $paths = func_get_args();

        array_shift($paths);

        return self::get_array($array, $paths);
    }

    /**
     * Gets the value deep inside $array using $string to find it.
     *
     * @param array $array
     * @param string $string
     * @param bool $shift
     * @return mixed
     */
    public static function get_string(&$array, $string, $shift = FALSE)
    {
        $paths = array();

        parse_str($string, $paths);

        $paths = self::recursive_keys($paths);

        if ($shift)
        {
            array_shift($paths);
        }

        return self::get_array($array, $paths);
    }

    /**
     * Gets the value deep inside $array using string values in $path to find it.
     *
     * @author Terrence Howard <chemisus@gmail.com>
     * @param array $array
     * @param array $paths
     * @return mixed
     */
    public static function get_array(&$array, $paths)
    {
        if (!is_array($paths))
        {
            return NULL;
        }

        $tmpArray = self::smart_cast($array);

        while (count($paths))
        {
            $key = array_shift($paths);

            if (!array_key_exists($key, (array)$tmpArray))
            {
                return NULL;
            }

            $tmpArray = &$tmpArray[$key];
        }

        return $tmpArray;
    }

    /**
     * Sets the value deep inside $array using $path to find it.
     *
     * @author Terrence Howard <chemisus@gmail.com>
     * @param array $array
     * @param mixed $value
     * @param string $path ...
     * @return array
     */
    public static function set(&$array, $value, $path)
    {
        $paths = func_get_args();

        array_shift($paths);

        array_shift($paths);

        return self::set_array($array, $value, $paths);
    }

    /**
     * Sets the value deep inside $array using $string to find it.
     *
     * @param array $array
     * @param mixed $value
     * @param string $string
     * @param bool $shift
     * @return mixed
     */
    public static function set_string(&$array, $value, $string, $shift = FALSE)
    {
        $paths = array();

        parse_str($string, $paths);

        $paths = self::recursive_keys($paths);

        if ($shift)
        {
            array_shift($paths);
        }

        return self::set_array($array, $value, $paths);
    }

    /**
     * Sets the value deep inside $array using string values in $path to find it.
     *
     * @author Terrence Howard <chemisus@gmail.com>
     * @param array $elem
     * @param mixed $value
     * @param array $paths
     * @return array
     */
    public static function set_array(&$array, $value, $paths)
    {
        $array = &self::smart_cast($array);

        $elem = &$array;

        if (!is_array($paths))
        {
            return $array;
        }

        while (count($paths))
        {
            $key = array_shift($paths);

            if (is_null($key))
            {
                $elem = &$elem[];
            }
            else
            {
                $elem = &$elem[$key];
            }
        }

        if (is_array($elem) AND is_array($value))
        {
            $elem = arr::merge($elem, $value);
        }
        else
        {
            $elem = $value;
        }

        return $array;
    }

    /**
     * This function will append the value to an existing value in an array if
     * one exists at the provided path, otherwise it is added as new.
     *
     * @param array $array
     * @param mixed $value
     * @param string $paths
     * @return array
     */
    public static function append(&$array, $value, $path)
    {
        $paths = func_get_args();

        array_shift($paths);

        array_shift($paths);

        return self::append_array($array, $value, $paths);
    }

    /**
     * This function will append the value to an existing value in an array if
     * one exists at the provided path, otherwise it is added as new.  If the
     * optional parameter shift is true then the base name of the variable will
     * be ignored.
     *
     * @param array $array
     * @param mixed $value
     * @param string $string
     * @param bool $shift
     * @return array
     */
    public static function append_string(&$array, $value, $string, $shift = FALSE)
    {
        $paths = array();

        parse_str($string, $paths);

        $paths = self::recursive_keys($paths);

        if ($shift)
        {
            array_shift($paths);
        }

        return self::append_array($array, $value, $paths);
    }

    /**
     * This function will append the value to an existing value in an array if
     * one exists at the provided path, otherwise it is added as new.
     *
     * @param array $array
     * @param string $value
     * @param array $paths
     * @return array
     */
    public static function append_array(&$array, $value, $paths)
    {
        if (($existing = self::get_array($array, $paths)))
        {
            if (is_string($existing))
            {
                $value = $existing .self::$append_string_separator .$value;

                $value = trim($value, self::$append_string_separator);
            }
            else if (is_array($existing))
            {
                $value = arr::merge($existing, (array)$value);
            }
            else
            {
                throw new Exception('Unable to append array values of type ' .gettype($value));
            }
        }

        return self::set_array($array, $value, $paths);
    }

    /**
     * This is a doctrine aware function that will accept a mixed var
     * and attempt to return an array.
     *
     * @param mixed var to convert
     * @return array
     */
    public static function smart_cast($mixed)
    {
        if (is_array($mixed))
        {
            return $mixed;
        }
        else if (is_object($mixed) AND method_exists($mixed, 'toArray'))
        {
            return $mixed->toArray();
        }
        else
        {
            return (array)$mixed;
        }
    }

    /**
     * This will create a recursive listing of all keys in a multidimensional
     * array
     *
     * @param array
     * @param array Used internally during recursion
     * @return array
     */
    public static function recursive_keys($array, &$keys = array())
    {
        foreach ($array as $key => $value)
        {
            $keys[] = $key;

            if (is_array($value))
            {
                self::recursive_keys($array[$key], $keys);
            }
        }

        return $keys;
    }

    /**
     * Will call $function on each object in $objects with $params as parameters.
     *
     * @author Terrence Howard <chemisus@gmail.com>
     * @param array $objects
     * @param string $function
     * @param array $params
     * @param string $implode
     * @return array|string
     */
    public static function call($objects, $function, $params = NULL, $implode = FALSE)
    {
        $objects = (array)$objects;

        $return = array();

        foreach ($objects as $key => $object)
        {
            $return[$key] = call_user_func_array(array($object, $function), (array)$params);
        }

        if ($implode !== FALSE)
        {
            $return = implode($implode, $return);
        }

        return $return;
    }

    /**
     * Checks if $subset's keys are a subset of $super's keys.
     *
     * @author Terrence Howard <chemisus@gmail.com>
     * @param array $super
     * @param array $subset
     * @return boolean
     */
    public static function subset($super, $subset)
    {
        if (count($subset) > count($super))
        {
            return false;
        }

        $sub = array_intersect_key($subset, $super);

        return count($sub) == count($subset) ? $sub : FALSE;
    }

    /**
     * Merges two arrays recursivly, in a disctinct manor
     *
     * @param array Array 1
     * @param array Array 2
     * @return array
     */
    public static function merge_recursive_distinct (array &$array1, array &$array2)
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
   *  NOTICE  NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE
   *
   *   The functions bellow this notice are depreciated and will be removed
   *   in the following sprints.
   *
   *            !! DO NOT USE THE FUNCTIONS BELLOW THIS NOTICE !!
   *
   *  NOTICE  NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE
   *
   */


























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
