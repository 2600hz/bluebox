<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Anonymous_Statistics.php - collects and send anonymous usage statistics.
 *
 * @author Karl Anderson
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
class Anonymous_Statistics
{
    /**
     * @var object the singleton implementation
     */
    private static $instance = NULL;

    /**
     * @var string a unique identifier for this installation
     */
    private static $anonymous_id = '';

    /**
     * @var object Kohanas cache instance
     */
    public static $cache = NULL;

	/**
	 * The constructor loads the anonymous_id from kohana config or gens a new
     * one if necessary.  It also loads the cache object into the class member $cache.
     *
	 * @return void
	 */
    public function __construct()
    {
        self::$cache = Cache::instance();

        $anonymous_id = Kohana::config('core.anonymous_id');

        if (empty($anonymous_id))
            $anonymous_id = strtoupper(md5(uniqid(rand(), true)));

       self::$anonymous_id = $anonymous_id;
    }

    /**
     * implement a singleton, create if needed
     *
	 * @return mixed object|false
     */
    public static function init()
    {
        if (empty(Anonymous_Statistics::$instance)) {
            Anonymous_Statistics::$instance = new Anonymous_Statistics();
        }
        if (!Kohana::config('core.anonymous_statistics')) {
            self::$cache->delete_tag('anonymous_statistics');
            return false;
        }

        return Anonymous_Statistics::$instance;
    }

    /**
     * Add a message to the statistics
     *
     * @param mixed $msg The a string or array to use as the message
     * @param string $key Optional parent $msg tag name
     * @param string $reporter Optional name of the module adding the message
     * @param string $id Optional id of the tag, if used and tag exists this will cause overwrite
	 * @return bool true if statistic collection allowed, otherwise false
     */
    public static function addMsg($msg, $key = 'item', $reporter = 'unknown', $id = 'none')
    {
        // If the user has disabled anonymous_statistic collection this is where we stop....
        if (!Kohana::config('core.anonymous_statistics'))
            return false;

        // If there is noting to add, skip adding it :)
        if (empty($msg))
            return true;

        // Initialize (if necessary) our instance so we can utilize our members
        self::init();

        // Get any current statistics
        $current = self::$cache->get(self::$anonymous_id);

        // If we have current stats then we will append otherwise start a new XML sheet
        if (!empty($current))
            $xml = new SimpleXMLElement($current);
        else
            $xml = new SimpleXMLElement('<?xml version=\'1.0\' standalone=\'yes\'?><root><anonymous_id>' .self::$anonymous_id .'</anonymous_id><messages></messages></root>');

       // If no key name was provided or it isnt a string then use a default
       if (empty($key) || !is_string($key))
            $key = 'item';

       // If we where given a non-default id then try to find it
       if (!empty($id) && $id != 'none')
       {
            $message = $xml->xpath('//message[@id="' .$id. '"]');
            if (!empty($message))
                $message = reset($message);
       }

       // If we are not appeding to an existing child then create a new one
       if (empty($message))
       {
            $message = $xml->messages->addChild('message');
            $message->addAttribute('reporter', $reporter);
            $message->addAttribute('date', date('U'));
            $message->addAttribute('id', $id);
       }

       // Recursivly add to XML
       self::_addMixed($msg, $key, $message);

       // Save our changes back
       self::$cache->set(self::$anonymous_id, $xml->asXML(), 'anonymous_statistics', 0);

       // Return
       return true;
    }

    /**
     * This function deletes all gathered statistics
     * @return void
     */
    public static function clear()
    {
        self::init();
        self::$cache->delete_tag('anonymous_statistics');
    }

    /**
     * This function returns the anonymous_id currently used
     * @return string
     */
    public static function getID()
    {
        self::init();
        return self::$anonymous_id;
    }

    /**
     * This function sends all gathered statistics
     * @return void
     * @Todo This is stub code, replace me
     */
    public static function send()
    {
        self::init();
        
        $sendSucceeded = true;

        $collectedStats = self::$cache->get(self::$anonymous_id);

        /**
         * TODO: send the $collectedStats string somewhere
         */

        if ($sendSucceeded)
            self::clear();
    }

    /**
     * This function recursively handles a mixed message body
     * adding child tags where necessary.  Note: arrays with
     * numerical indexs will result in tags called {key}_{index}
     * @param mixed $mixed The mixed element to operate on
     * @param string $key The name to use for any non-associative array child tags
     * @param object $parent the parent tag
     * @return void
     */
	private static function _addMixed($mixed, $key, &$parent)
    {
		if (is_array($mixed)) {
			foreach( $mixed as $index => $mixedElement ) {
                if (is_int($index))
                    $index = $key .'_' .$index;
                if (is_array($mixedElement))
                {
                   if (!empty($parent->$index))
                        $subParent = $parent->$index;
                    else
                        $subParent = $parent->addChild($index);
                    self::_addMixed($mixedElement, $key, $subParent);
                } else {
                    if (!empty($parent->$index))
                        $parent->$index = $mixedElement;
                    else
                        $parent->addChild($index, $mixedElement);
                }
			}
		} else {
            if (!empty($parent->$key))
                $parent->$key = $mixed;
            else
                $parent->addChild($key, $mixed);
		}
	}
}
