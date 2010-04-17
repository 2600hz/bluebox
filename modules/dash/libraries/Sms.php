<?php
abstract class Sms
{
	public $apid_id = "";
	public $login = "";
	public $password = "";
	protected $errorStack = array();

	
	abstract public function send($destination, $message);
	abstract public function auth($login, $password, $api_id = null);
	abstract public function getReponse();
	
	public function getErrors()
	{
		return $errorStack;
	}
	
	
	private function addError($msg)
	{
		$this->errorStack[] = $msg;
	}
	
	// The parameterized factory method
}

