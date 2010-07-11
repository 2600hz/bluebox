<?php
/**
Usage:
	$sms = Sms::setDriver('Clickatell');
	$sms->auth('login', 'password', '1231234');
	$sms->send('1414XXXXXXX', 'Hello world');

 */ 

class Driver_Clickatell
{
	private $ch;
	private $auth = false;
	private $sessionId;
	private $sessionTimeout = 0;
	private $baseUrl;
	
	public function __construct()
	{
		$this->auth = false;
		$this->baseUrl = 'https://api.clickatell.com';
	}
	
	public function getReponse()
	{
		
	}
	
	public function send($to, $message)
	{
		if(!$this->isSessionValid())
		{
			$msg = __('You must re-authenticate again');
			$this->errors[] = $msg;
			Kohana::log('error', $msg);

			return false;
		}
		
		if(strlen($message) > 160)
		{
			$msg = __('Message exceeds 160 characters');
			$this->errors[] = $msg;
			Kohana::log('error', $msg);
			return false;
			
		}
		
		$message = urlencode($message);
		$url = sprintf("%s/http/sendmsg?session_id=%s&to=%s&text=%s", $this->baseUrl, $this->sessionId, $to, $message);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET , TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		curl_close($ch);
		Kohana::log('info', "SMS gateway response: " . $response);
		return true;
	}
	
	public function auth($login, $password, $api_id = null)
	{

		if($this->isSessionValid())
		{
			return true;
		}
		$url = sprintf("%s/http/auth?user=%s&password=%s&api_id=%s", $this->baseUrl, $login, $password, $api_id);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET , TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		curl_close($ch);

		//curl get respoinse
		
		$return = explode(':', $response);
		if(trim($return[0]) == 'ERR')
		{
			$msg = trim($return[1]);
			Kohana::log('info', 'Failed to authenticate to SMS gateway');
			$this->errorStack[] = trim($return[1]);
			return false;
		} elseif(trim($return[0]) == 'OK') {
			$this->auth = true;
			$this->resetSessionTimeout();
			$this->sessionId = trim($return[1]);
			Kohana::log('info', 'Authenticated to SMS gateway');
			return true;
		}
	}

	private function resetSessionTimeout()
	{
		$this->sessionTimeout = mktime() + 15 * 60; //15 minute timeout
	}
	
	public function isSessionValid() //maybe need a getSessionId????
	{
		//use memcache to store across page requests!!!
		if(!$this->auth)
		{
			return false;
		} elseif(mktime() > $this->sessionTimeout) {
			$this->errors[] = __('Session expired');
			$this->auth = false;
		} else {
			$this->auth = true;
		}
		return $this->auth;
	}
}
