<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Infector
 * @author     K Anderson <bitbashing@gmail.com>
 * @author     Bryant Lee
 * @license    Mozilla Public License (MPL)
 */
class inflector extends inflector_Core
{
    public static function generatePassword($length = 8)
    {
        $consonants = 'bdghjmnpqrstvz';
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
        $consonants .= '1234567890';

        $vowels = 'aeuy';
        $vowels .= "AEUY";

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
                if ($alt == 1) {
                        $password .= $consonants[(rand() % strlen($consonants))];
                        $alt = 0;
                } else {
                        $password .= $vowels[(rand() % strlen($vowels))];
                        $alt = 1;
                }
        }
        // at least one digit
        $password = substr_replace($password, rand(0, 9), rand(1, $length), 1);

        return $password;
    }

    public static function humanizeModelName($modelName, $count = 1)
    {
        if (is_object($modelName))
        {
            $modelName = get_class($modelName);
        }

        $modelName = preg_replace('/([A-Z])/', ' $1', self::lcfirst($modelName));

        // Remove garbage
        $modelName = trim($modelName);

        if (is_string($count))
        {
                // Convert to integer when using a digit string
                $count = (int) $count;
        }

        // Do nothing with singular
        if ($count === 1)
                return ucfirst($modelName);

        // Cache key name
        $key = 'plural_'.$modelName.$count;

        if (isset(self::$cache[$key]))
                return ucfirst(self::$cache[$key]);

        if (inflector::uncountable($modelName))
                return ucfirst(self::$cache[$key] = $modelName);

        if (empty(self::$irregular))
        {
                // Cache irregular words
                self::$irregular = Kohana::config('inflector.irregular');
        }

        if (isset(self::$irregular[$modelName]))
        {
                $modelName = self::$irregular[$modelName];
        }
        elseif (preg_match('/[sxz]$/', $modelName) OR preg_match('/[^aeioudgkprt]h$/', $modelName))
        {
                $modelName .= 'es';
        }
        elseif (preg_match('/[^aeiou]y$/', $modelName))
        {
                // Change "y" to "ies"
                $modelName = substr_replace($modelName, 'ies', -1);
        }
        else
        {
                $modelName .= 's';
        }

        // Set the cache and return
        return ucfirst(self::$cache[$key] = $modelName);
    }

    public static function lcfirst($str)
    {
        if ( false === function_exists('lcfirst') )
        {
            return (string)(strtolower(substr($str,0,1)).substr($str,1));
        }

        return lcfirst( $str );
    }
}