<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * telephony.php - telephony driver class
 *
 * This is the base class that all telephony drivers must extend.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage Core
 */

interface Telephony_Driver_Interface {
    public static function getInstance();

    public function load($options);

    public function save($options = NULL);

    public function render();

    public function reset();
    
    public function commit();
}

abstract class Telephony_Driver implements Telephony_Driver_Interface {
    /**
     * Instance of this class.
     * @var Telephony_Driver
     */
    protected static $instance;

    /**
     * List of sections that exist in the dialplan. Note that this array MUST be in the same order you want things to appear in the dialplan
     * @var array
     */
    protected static $dialplanSections = array('network', 'conditioning', 'preroute', 'postroute', 'preanswer', 'postanswer', 'main', 'prenumber', 'postnumber', 'postexecute');

    /**
     * For dialplan, current context we are working in
     * @var string
     */
    protected $currentContext = NULL;

    /**
     * For dialplan, current section we are working in
     * @var string
     */
    protected $currentSection = NULL;

    public static function getDialplanSections()
    {
        return self::$dialplanSections;
    }

    public static function getCurrentContext()
    {
        return self::$instance->currentContext;
    }

    public static function getCurrentSection()
    {
        return self::$instance->currentSection;
    }

    private function runGlobalEvents($context, $section, $obj)
    {
        // Mark where we are
        Kohana::log('debug', 'Global dialplan hooks: Creating extensions for ' . $section);
        if ($context !== FALSE) {
            $this->currentContext = $context;
        }
        $this->currentSection = $section;

        // Run the events
        Event::run('_telephony.' . $section, $obj);

        // Clear where we are
        Kohana::log('debug', 'Global dialplan hooks: Done creating extensions for ' . $section);
        if ($context !== FALSE) {
            $this->currentContext = NULL;
        }
        $this->currentSection = NULL;
    }

    /**
     * Add anything that detects networking or global based variables related to routing
     * Note: Some switches don't support some features in this context (like NAT detection)
     * @param <string> $number Extension number we are generating dialplan for
     */
    public function network($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'network', $obj);
    }

    /**
     * Condition existing switch variables (strip +1, trim PRI digits, etc.)
     * @param Bluebox_Record $obj
     */
    public function conditioning($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'conditioning', $obj);
    }

    /**
     * Pre-routing decisions - who is this call really going to?
     * Identify the correct user for the call
     * Do any caller-id based routing here, or set queue prioritizations
     * @param Bluebox_Record $obj
     */
    public function preRoute($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'preroute', $obj);
    }

    /**
     * Post-routing decisions - now that we know who we're calling
     * are we allowed to call them? How do we reach them?
     * Blacklist, find-me/follow-me, day/night, etc. would go here
     * @param Bluebox_Record $obj
     */
    public function postRoute($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'postroute', $obj);
    }

    /**
     * Are we doing any special items before answering?
     * Examples might be setting music on hold, setting ringback style
     * @param Bluebox_Record $obj
     */
    public function preAnswer($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'preanswer', $obj);
    }

    /**
     * Do you want to do anything for this call before it reaches it's destination?
     * Examples: play "your call is being recorded" greeting, a tone, starting call-recording, etc.
     * @param Bluebox_Record $obj
     */
    public function postAnswer($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'postanswer', $obj);
    }

    /**
     * If a call bridge/transfer fails, you can optionally add an alternate destination (such as voicemail)
     * @param Bluebox_Record $obj
     */
    public function preNumber(Bluebox_Record $obj = NULL)
    {
        // NOTE: $context is NULL here because pre & post number assumes the context is already set by the current macro/extension
        
        $this->runGlobalEvents(FALSE, 'prenumber', $obj);
    }

    /**
     * If a call bridge/transfer fails, you can optionally add an alternate destination (such as voicemail)
     * @param Bluebox_Record $obj
     */
    public function postNumber(Bluebox_Record $obj = NULL)
    {
        // NOTE: $context is NULL here because pre & post number assumes the context is already set by the current macro/extension

        $this->runGlobalEvents(FALSE, 'postnumber', $obj);
    }

    /**
     * This is not guaranteed to work on all platforms - should not be used if it can be avoided.
     * Occurs after execute. Useful for surveys, logging, reporting, etc.
     * @param Bluebox_Record $obj
     */
    public function postExecute($context, Bluebox_Record $obj = NULL)
    {
        $this->runGlobalEvents($context, 'postexecute', $obj);
    }

    public function resetContext()
    {
        $this->currentContext = NULL;
    }

    public function resetSection()
    {
        $this->currentSection = NULL;
    }
}
