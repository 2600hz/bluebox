<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Input
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Input extends Input_Core
{
    protected $unfiltered_inputs = array();

    public function __construct()
    {
        $preserve = array('_REQUEST', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER', '_ENV', '_SESSION');

        foreach ($preserve as $input)
        {
            if (!isset($GLOBALS[$input]))
            {
                continue;
            }

            $this->unfiltered_inputs[strtolower($input)] = $GLOBALS[$input];
        }

        parent::__construct();

        // loop each of the request vars
        foreach ($_REQUEST as $key => $value)
        {
            // if we find a var that begins with __ that is a marker for a
            // checkbox
            if (substr($key, 0, 2) == '__' )
            {
                // setup and array removing the __ in the key
                $uncheckedValue = array(substr($key, 2) => $value);

                // merge this into the request array, NOT replacing any existing value
                $_REQUEST = arr::merge_recursive_distinct($uncheckedValue, $_REQUEST);

                // remove our temporary tracker
                unset($_REQUEST[$key]);

                // see if this var came from a post
                if (array_key_exists($key, $_POST))
                {
                    // merge this into the post array, NOT replacing any existing value
                    $_POST = arr::merge_recursive_distinct($uncheckedValue, $_POST);

                    unset($_POST[$key]);
                }

                // see if this var came from a get
                if (array_key_exists($key, $_GET))
                {
                    // merge this into the get array, NOT replacing any existing value
                    $_GET = arr::merge_recursive_distinct($uncheckedValue, $_GET);

                    unset($_GET[$key]);
                }
            }
        }
    }   

    public function get_unfiltered($key)
    {
        $key = strtolower($key);

        if (!array_key_exists($key, $this->unfiltered_inputs))
        {
            return array();
        }
        
        return $this->unfiltered_inputs[$key];
    }
}
