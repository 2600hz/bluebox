<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * i18n.php - Wrapper for internationalization and localization
 *
 *
 * @author K Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Core
 */
class i18n
{
    /**
     * TODO: Write out the i18n functions
     *   Possible resources
     *   a. http://devzone.zend.com/article/4799
     *   b. http://codex.wordpress.org/I18n_for_WordPress_Developers
     *
     * Tasks that i18n needs to address (as per resource a)
     *   a. Translating the text
     *   b. Sorting textual data according to local rules.
     *   c. Displaying numbers. Characters used to represent number properties (sign, decimal point, thousand separator) vary widely.
     *   d. Date and time formatting, whether using a different calendar or using a local format to represent a base calendar
     *   e. Displaying time in the local timezone, and dealing with users in multiple timezones.
     *   f. Representing money values and currencies.
     *   g. Displaying ordinal and cardinal numbers, i.e. numbers representing order (1st, 2nd) and quantity (1 error, 2 errors).
     *   h. Rendering parameterized strings — where the values are inserted in pre-existing templates, such as printf() — according to local grammar rules.
     *   i. Breaking text into letters, words and sentences according to local rules.
     */
    /**
     * @var object the singleton implementation
     */
    private static $instance;
    /**
     * @var array The locale info used to generate to proper output
     */
    private static $locale = array();
    /**
     * @var string this holds the root message currently under translation
     */
    private static $message = '';
    /**
     * @var array this is an array of the additional arguments passed during this translation
     */
    private static $args = array();
    /**
     * @var array The locale info used to generate to proper output
     */
    private static $options = array();
    public static $langs = array(
        'sq' => 'Albanian',
        'ar' => 'Arabic',
        'bg' => 'Bulgarian',
        'ca' => 'Catalan',
        'zh-CN' => 'Chinese',
        'hr' => 'Croatian',
        'cs' => 'Czech',
        'da' => 'Danish',
        'nl' => 'Dutch',
        'en' => 'English',
        'et' => 'Estonian',
        'tl' => 'Filipino',
        'fi' => 'Finnish',
        'fr' => 'French',
        'gl' => 'Galician',
        'de' => 'German',
        'el' => 'Greek',
        'iw' => 'Hebrew',
        'hi' => 'Hindi',
        'hu' => 'Hungarian',
        'id' => 'Indonesian',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'lv' => 'Latvian',
        'lt' => 'Lithuanian',
        'mt' => 'Maltese',
        'no' => 'Norwegian',
        'fa' => 'Persian ALPHA',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sr' => 'Serbian',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'es' => 'Spanish',
        'sv' => 'Swedish',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'uk' => 'Ukrainian',
        'vi' => 'Vietnamese'
    );
    /**
     * Create an instance if one does not already exists
     * or return the current instance
     *
     * @return void
     */
    public static function instance($args = '')
    {
        //instantiate i18n if necessary and store the instance
        if (!isset(self::$instance)) {
            self::$instance = new i18n();
        }
        // If there are no args passed then return the instance
        // without setting the iteration vars
        if (empty($args)) return self::$instance;
        // clear the iteration specific values
        self::$message = '';
        self::$args = self::$options = array();
        // If there is an array of arguments then the first is assumed
        // to be the message and the rest are stored in args
        if (is_array($args)) {
            self::$message = reset($args);
            array_shift($args);
            self::$args = $args;
        } else {
            // if the args are not an array then we recieved just a message
            self::$message = $args;
        }
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * The class constructor is responsible for setting the locale
     */
    private function __construct($locale = '')
    {
    }
    /**
     * This function translates a given string and returns the
     * translation if successfull or the original string
     */
    public function translate($message = NULL)
    {
        //preform translation
        $session = Session::instance();
        $lang = $session->get('lang', 'en');
        if (!empty($message) && $lang != 'en') {
            if (class_exists('RosettaManager')) {
                try {
                    $r = RosettaManager::instance();
                    $msg = $r->setTo($lang)->translate($message);
                    if (!empty($msg)) return $msg;
                }
                catch(exception $e) {
                }
            }
        }
        return $message;
    }
    /**
     * This provides the ngettext functionality
     */
    public function ngettext($n)
    {
        self::$message = ngettext(self::$message, reset(self::$args) , $n);
        if (empty(self::$options['ngettext_sprintf_disable'])) {
            self::$message = sprintf(self::$message, $n);
        }
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * This provides sprintf functionality
     */
    public function sprintf()
    {
        $args = array(
            self::$message
        );
        if (is_array(self::$args)) {
            $args = array_merge($args, self::$args);
            self::$message = call_user_func_array('sprintf', $args);
        }
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * This function echos the current message inline
     */
    public function e()
    {
        echo $this->translate(self::$message);
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * Sometimes it is necessary to return the string explicitly
     */
    public function s()
    {
        return (string)$this->translate(self::$message);
    }
    /**
     * This method allows bulk loading of option key/values
     */
    public function loadOptions($options)
    {
        if (is_array($options)) {
            self::$options = $options;
        }
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * This method allows bulk merge of option key/values
     */
    public function mergeOptions($options)
    {
        if (is_array($options)) {
            self::$options = arr::merge($options, self::$options);
        }
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * Sets an i18n option for this iteration, without breaking a chain
     */
    public function set($name, $value)
    {
        self::$options[$name] = $value;
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * This function allows the unsetting of i18n options
     */
    public function __unset($name)
    {
        if (array_key_exists($name, self::$options)) {
            unset(self::$options[$name]);
        }
        // return the instance to allow method chaining
        return self::$instance;
    }
    /**
     * When this class is converted to a string return the current message
     */
    public function __toString()
    {
        return (string)$this->translate(self::$message);
    }
}
/**
 * This function is a convience wrapper for preforming a simple translation
 *
 * @return string
 */
function __($message)
{
    $session = Session::instance();
    $lang = $session->get('lang', 'en');
    if (!empty($message) && $lang != 'en') {
        return i18n::instance($message)->s();
    }
    return $message;
}
/**
 * This function is a convience wrapper for accessing the translation
 * and localization class
 *
 * @return object the singleton i18n instance
 */
function i18n()
{
    $args = func_get_args();
    return i18n::instance($args);
}
