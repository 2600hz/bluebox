<?php defined('SYSPATH') or die('No direct access allowed.');

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
}
