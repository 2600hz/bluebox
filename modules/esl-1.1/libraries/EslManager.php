<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * EslManager.php - This wraps the two types of ESLconnection and provides additional
 *      convience functions
 *
 * @author K Anderson
 * @license LGPL
 * @package Esl
 */
class EslManager
{
    private $esl = NULL;

    private $extension = FALSE;

    static private $instance;

    /**
     * Initialize the ESL connection.  This must be called first
     * @access public
     * @static
     * @return bool
     */
    public function __construct()
    {
	$host = Kohana::config('freeswitch.ESLHost');
	$port =  Kohana::config('freeswitch.ESLPort');
	$password = Kohana::config('freeswitch.ESLAuth');

	if (!extension_loaded("ESL"))   // Is the PHP ESL extension loaded? If not, failback to socket
	{
            $this->esl = new ESLconnection($host, $port, $password); // socket connection
            $this->extension = FALSE;
        } else {
                include_once(MODPATH .'esl-1.1/assets/ESL.php');
            $this->esl = new ESLconnection($host, $port, $password); // FreeSWITCH ESL Swigged class
            $this->extension = TRUE;
	}
    }

    public static function getInstance()
    {
        if (!self::$instance)
        {
            return self::$instance = new EslManager();
        }

        return self::$instance;
    }

    public static function eventReloadXML()
    {
        self::getInstance()->reloadxml();
    }

    public static function eventReloadACL()
    {
        self::getInstance()->reloadacl();
    }

    public static function eventReloadXMLCDR()
    {
        self::getInstance()->sendRecv('api reload mod_xml_cdr');
    }

    public static function eventReloadSofia()
    {
        self::getInstance()->reload('mod_sofia');
    }

    public static function eventReloadDingaling()
    {
        self::getInstance()->reload('mod_dingaling');
    }

    /*
     * Clean up connection when script is done executing.
     * If connected, try to disconnect.
     */
    public function __destruct()
    {
        if ($this->isConnected()) {
               return $this->esl->disconnect();
       }
    }

    /**
     * This lets us determine how we are accessing ESL
     */
    public function isExtension() {
        return $this->extension;
    }

    /**
     * Returns the connection status of the current connection
     */
    public function isConnected()
    {
        if(!is_object($this->esl)) {
            return FALSE;
        } else {
            return $this->esl->connected();
        }
    }

    /**
     * gets the raw ESLconnection
     */
    public function getESL() {
        return $this->esl;
    }

    /**
     * check if a command execution response was successfull
     */
    public function isSuccessfull($event = NULL) {
        if ($event instanceof ESLevent) {
            $reply = $event->getHeader('Reply-Text');
            if (strstr($reply, '+OK')) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * This will return a string froma event with the most appropriate
     * meaning
     */
    public function getResponse($event = NULL) {
        if ($event instanceof ESLevent) {
            
            $body = $event->getBody();
            if (!empty($body)) return $body;

            $reply = $event->getHeader('Reply-Text');
            if (!empty($reply)) return $reply;

            return $event->serialize();
        }
        
        if (!is_string($event)) {
            return 'Command execution failed.';
        } else {
            return $event;
        }
    }

    /**
     * Convience wrapper for nat operations
     */
    public function nat($operation)
    {
        if (!$this->isConnected()) return FALSE;

        $esl = $this->esl;

        switch($operation)
        {
            case 'status':
                return $esl->api('nat_map',' status');
                break;

            case 'reinit':
                return $esl->api('nat_map',' reinit');
                break;

            case 'republish':
                return $esl->api('nat_map',' republish');
                break;

            default:
                return "NAT operation $operation not valid";
        }
    }
    
    /**
     * Make an outoging call by connecting a remote device to a local one
     */
    public function originateLocal($external, $internal, $from = '')
    {
            //user->CallerID now
            $this->sendBgAPI("originate",
            sprintf("{origination_caller_id_number=1231231234,ignore_early_media=true,originate_timeout=30}sofia/external/%s@trunk1.bluebox.com %d", $external, $internal));
    }

    /**
     * Make an outoging call by connecting a remote device to a remote one
     */
    public function originateRemote($external, $internal, $from = '')
    {
            //user->CallerID now
            $this->sendBgAPI("originate",
            sprintf("{origination_caller_id_number=1231231234,ignore_early_media=true,originate_timeout=30}sofia/external/%s@trunk1.bluebox.com %d", $external, $internal));
    }

    /**
     * This lets the eslManager wrap all the ESLconnection methods
     */
    public function __call($name, $arguments) {
        if (!$this->isConnected()) return FALSE;
        
        $esl = $this->esl;

        // These are some convience wrappers to support common commands
        switch(strtolower($name)) {

            case 'version':
                return $esl->api('version');
                break;

            case 'status':
                return $esl->api('status');
                break;

            case 'reloadacl':
                return $esl->api('reloadacl');
                break;

            case 'reloadxml':
                return $esl->api('reloadxml');
                break;

            case 'reload':
                array_unshift($arguments, 'bgapi', 'reload', '-f');
                return  $esl->sendRecv(implode(' ', $arguments));
                break;

            case 'sofia':
                array_unshift($arguments, 'sofia');
                return call_user_func(array($esl, 'api'), implode(' ', $arguments));
                break;

            case 'show':
                array_unshift($arguments, 'show');
                return call_user_func(array($esl, 'api'), implode(' ', $arguments));
                break;

            case 'channels':
                return $esl->api('show', 'channels');
                break;

            case 'calls':
                return $esl->api('show', 'calls');
                break;

            default:
                if (!method_exists($this->esl, $name)) return FALSE;
                return call_user_func(array($this->esl, $name), implode(' ', $arguments));
        }
    }
    
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
}
