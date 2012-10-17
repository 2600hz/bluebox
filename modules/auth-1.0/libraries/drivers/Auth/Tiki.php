<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

class Http_Exception extends Exception{
    const NOT_MODIFIED = 304; 
    const BAD_REQUEST = 400; 
    const NOT_FOUND = 404; 
    const NOT_ALOWED = 405; 
    const CONFLICT = 409; 
    const PRECONDITION_FAILED = 412; 
    const INTERNAL_ERROR = 500; 
}

class Http_Stat
{
    private $_status = null;
    private $_type   = null;
    private $_url    = null;
    private $_params = null;
    private $_success = null;
	private $_data = null;
	
    function __construct($status, $type, $url, $params, $success = false, $data = null)
    {
        $this->_status = $status;
        $this->_type   = $type;
        $this->_url    = $url;
        $this->_params = $params;
	$this->_success = $success;
	$this->_data = $data;
    }
    
    function getStatus()
    {
        return $this->_status;
    }
    
    function getType()
    {
        return $this->_type;
    }
    
    function getUrl()
    {
        return $this->_url;
    }
    
    function getParams()
    {
        return $this->_params;
    }
	
	function getSuccess()
	{
		return $this->_success;
	}

	function getData()
	{
		return $this->_data;
	}
}

class Http
{
    private $_host = null;
    private $_port = null;
    private $_user = null;
    private $_pass = null;
    private $_protocol = null;

    const HTTP  = 'http';
    const HTTPS = 'https';
    
    private $_connMultiple = false;
    /**
     * Factory of the class. Lazy connect
     *
     * @param string $host
     * @param integer $port
     * @param string $user
     * @param string $pass
     * @return Http
     */
    static public function connect($host, $port = 80, $protocol = self::HTTP)
    {
        return new self($host, $port, $protocol, false);
    }
    
    /**
     *
     * @return Http
     */
    static public function multiConnect()
    {
        return new self(null, null, null, true);
    }

    private $_append = array();
    public function add($http)
    {
        $this->_append[] = $http;
        return $this;
    }
    
    private $_silentMode = false;
    /**
     *
     * @param bool $mode
     * @return Http
     */
    public function silentMode($mode=true)
    {
        $this->_silentMode = $mode;
        return $this;    
    }
    
    protected function __construct($host, $port, $protocol, $connMultiple)
    {
        $this->_connMultiple = $connMultiple;
        
        $this->_host     = $host;
        $this->_port     = $port;
        $this->_protocol = $protocol;
    }
    
    public function setCredentials($user, $pass)
    {
        $this->_user = $user;
        $this->_pass = $pass;
    }

    const POST   = 'POST';
    const GET    = 'GET';
    const DELETE = 'DELETE';

    private $_requests = array();
    /**
     * @param string $url
     * @param array $params
     * @return Http
     */
    public function post($url, $params=array())
    {
        $this->_requests[] = array(self::POST, $this->_url($url), $params);
        return $this;
    }

    /**
     * @param string $url
     * @param array $params
     * @return Http
     */
    public function get($url, $params=array())
    {
        $this->_requests[] = array(self::GET, $this->_url($url), $params);
        return $this;
    }
    
    /**
     * @param string $url
     * @param array $params
     * @return Http
     */
    public function delete($url, $params=array())
    {
        $this->_requests[] = array(self::DELETE, $this->_url($url), $params);
        return $this;
    }
    
    public function _getRequests()
    {
        return $this->_requests;
    }
    
    /**
     * POST request
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function doPost($url, $params=array())
    {
        return $this->_exec(self::POST, $this->_url($url), $params);
    }

    /**
     * GET Request
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function doGet($url, $params=array())
    {
        return $this->_exec(self::GET, $this->_url($url), $params);
    }
    
    /**
     * DELETE Request
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function doDelete($url, $params=array())
    {
        return $this->_exec(self::DELETE, $this->_url($url), $params);
    }

    private $_headers = array();
    /**
     * setHeaders
     *
     * @param array $headers
     * @return Http
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
        return $this;
    }

    /**
     * Builds absolute url 
     *
     * @param unknown_type $url
     * @return unknown
     */
    private function _url($url=null)
    {
        return "{$this->_protocol}://{$this->_host}:{$this->_port}/{$url}";
    }

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACEPTED = 202;

    /**
     * Performing the real request
     *
     * @param string $type
     * @param string $url
     * @param array $params
     * @return string
     */
    private function _exec($type, $url, $params = array())
    {
        $headers = $this->_headers;
        $s = curl_init();
        
        if(!is_null($this->_user)){
           curl_setopt($s, CURLOPT_USERPWD, $this->_user.':'.$this->_pass);
        }

        switch ($type) {
            case self::DELETE:
                curl_setopt($s, CURLOPT_URL, $url . '?' . http_build_query($params));
                curl_setopt($s, CURLOPT_CUSTOMREQUEST, self::DELETE);
                break;
            case self::POST:
                curl_setopt($s, CURLOPT_URL, $url);
                curl_setopt($s, CURLOPT_POST, true);
                curl_setopt($s, CURLOPT_POSTFIELDS, $params);
                break;
            case self::GET:
                curl_setopt($s, CURLOPT_URL, $url . '?' . http_build_query($params));
                break;
        }

        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
        $_out = curl_exec($s);
        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
        switch ($status) {
            case self::HTTP_OK:
            case self::HTTP_CREATED:
            case self::HTTP_ACEPTED:
                $out = $_out;
                break;
            default:
                if (!$this->_silentMode) {
                    throw new Http_Exception("http error: {$status}", $status);
                }
        }
        return $out;
    }
    
    public function run()
    {
        if ($this->_connMultiple) {
            return $this->_runMultiple();
        } else {
            return $this->_run();
        }
    }
    
    private function _runMultiple()
    {
        $out= null;
        if (count($this->_append) > 0) {
            $arr = array();
            foreach ($this->_append as $_append) {
                $arr = array_merge($arr, $_append->_getRequests());
            }
            
            $this->_requests = $arr;
            $out = $this->_run();
        }
        return $out;
    }
    
    private function _run()
    {
        $headers = $this->_headers;
        $curly = $result = array();

        $mh = curl_multi_init();
        foreach ($this->_requests as $id => $reg) {
            $curly[$id] = curl_init();
            
            $type   = $reg[0];
            $url    = $reg[1];
            $params = $reg[2];
            
            if(!is_null($this->_user)){
               curl_setopt($curly[$id], CURLOPT_USERPWD, $this->_user.':'.$this->_pass);
            }
            
            switch ($type) {
                case self::DELETE:
                    curl_setopt($curly[$id], CURLOPT_URL, $url . '?' . http_build_query($params));
                    curl_setopt($curly[$id], CURLOPT_CUSTOMREQUEST, self::DELETE);
                    break;
                case self::POST:
                    curl_setopt($curly[$id], CURLOPT_URL, $url);
                    curl_setopt($curly[$id], CURLOPT_POST, true);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $params);
                    break;
                case self::GET:
                    curl_setopt($curly[$id], CURLOPT_URL, $url . '?' . http_build_query($params));
                    break;
            }
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curly[$id], CURLOPT_HTTPHEADER, $headers);
            
            curl_multi_add_handle($mh, $curly[$id]);
        }
    
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            sleep(0.2);
        } while($running > 0);
    
        foreach($curly as $id => $c) {
            $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
            switch ($status) {
                case self::HTTP_OK:
                case self::HTTP_CREATED:
                case self::HTTP_ACEPTED:
                    $result[$id] = new Http_Stat($status, $type, $url, $params, true, curl_multi_getcontent($c));
                    break;
                default:
                    if (!$this->_silentMode) {
                        $result[$id] = new Http_Stat($status, $type, $url, $params, false);
                    }
            }
            curl_multi_remove_handle($mh, $c);
        }

        curl_multi_close($mh);
        return $result;
    }
}

defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Tiki Auth driver, relies on Doctrine driver
 * Note: this Auth driver does not support roles nor auto-login.
 *
 * $Id: File.php 3917 2009-04-02 03:06:22Z zombor $
 *
 * @package    Auth
 * @author     Robert Plummer
 * @copyright  (c) 2009 Darren Schreiber
 * @license    Mozilla Public License (MPL) v1.1
 */
class Auth_Tiki_Driver extends Auth_Driver
{
    /**
     * VERSION
     */
    const VERSION = '0.1';

    /**
     * Constructor loads the user list into the class.
     */
    public function __construct(array $config, $passwordOrig = '')
    {
        parent::__construct($config);
        
		include_once("Doctrine.php");
        $this->doctrine = new Auth_Doctrine_Driver($config);
		
        if (empty($passwordOrig) && !empty($_REQUEST['login']['password'])) {
	        $passwordOrig = $_REQUEST['login']['password'];
        }
        $this->passwordOrig = $passwordOrig;
    }

    /**
     * Logs a user in.
     *
     * @param   string   username
     * @param   string   password
     * @param   boolean  enable auto-login (not supported)
     * @return  boolean
     */
    public function login($username, $password, $remember, $requirePassword = TRUE)
    {
		$client = Http::connect('bluebox.tikisuite.org');
		$client->setCredentials($username, $this->passwordOrig);
		$status = $client->get('/tiki/tiki-index.php')->run();

		$tikiLogin = FALSE;

		if (isset($status[0])) {
			$tikiLogin = $status[0]->getSuccess();
		}
		
		if ($tikiLogin != TRUE) return $tikiLogin;
		
		//at this point the user exists in tiki
		$userExists = (strlen($this->password($username)) > 0 ? TRUE : FALSE);
		
		if (empty($userExists) && !empty($password)) {
			$userId = $this->createUser($username, $password);
			$deviceId = $this->createDevice(1, $username, $this->passwordOrig);
			$numberId = $this->createNumber($deviceId, $username);
		}
		
		return $this->complete_login($username);
		
		//we don't need to verify password here, it was done above from tiki server
        //return $this->doctrine->login($username, $password, $remember, $requirePassword);
    }

    /**
     * Forces a user to be logged in, without specifying a password.
     *
     * @param   mixed    username
     * @return  boolean
     */
    public function force_login($username)
    {
        return $this->doctrine->force_login($username);
    }

    /**
     * Get the stored password for a username.
     *
     * @param   mixed   username
     * @return  string
     */
    public function password($username)
    {
        return $this->doctrine->password($username);
    }

    /**
     * Generate and return a token that allows for resetting of a user's password
     * @param string $username Username of the user to reset
     * @return string Returns a token/hash that should be sent to a user via a confirmed method to allow for reset
     */
    public function resetToken($username)
    {
        return $this->doctrine->resetToken($username);
    }

    /**
     * Force a new password to be recorded, if a password reset token matches the stored token previously generated.
     * @param string $username
     * @param string $token Password token, generated and recorded by resetToken()
     * @param string $newPassword
     * @return boolean True if reset is successful
     */
    public function resetPassword($username, $token, $newPassword)
    {
        return $this->doctrine->resetPassword($username, $token, $newPassword);
    }

    public function getRealIpAddr()
    {
        return $this->doctrine->getRealIpAddr();
    }
	
	private function createUser($username, $password) {
		$emailParts = explode('@', $username);
		
		$user = new User();
		$user['first_name'] = $emailParts[0];
		$user['last_name'] = 'Tiki';
		$user['username'] = $username;
		$user['email_address'] = $username;
		$user['password'] = $password;
		$user['user_type'] = User::TYPE_NORMAL_USER;
		$user['location_id'] = 1;
		$user['account_id'] = 1;
		
		//save user
		Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
		$userId = $user->save();
		Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
		
		return $userId;
	}
	
	private function createDevice($userId, $username, $password) {
		$emailParts = explode('@', $username);
		
		$device = new Device();
		$device['name'] = $emailParts[0];
		$device['user_id'] = $userId;
		$device['context_id'] = 1;
		$device['account_id'] = 1;
		$device['type'] = "SipDevice";
		$device['plugins'] = array(
			'sip'=>array(
				'username'=> $username,
				'password'=> $password,
			),
		);

		//save device
		Doctrine::getTable('Device')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
		$deviceId = $device->save();
		Doctrine::getTable('Device')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
		
		return $deviceId;
	}
	
	private function createNumber($deviceId, $username) {
		$emailParts = explode('@', $username);
		
		$number = new Number();
		$number['number'] = $emailParts[0];
		$number['type'] = Number::TYPE_INTERNAL;
		$number['status'] = Number::STATUS_NORMAL;
		$number['location_id'] = 1;
		$number['account_id'] = 1;
		$number['foreign_id'] = $deviceId;
		
		//save number
		Doctrine::getTable('Number')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
		$numberId = $number->save();
		Doctrine::getTable('Number')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
		
		return $numberId;
	}
} // End Auth_File_Driver
