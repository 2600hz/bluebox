<?php

class Dash_Controller extends Bluebox_Controller
{
	
	public function index()
	{

	}
	
	public function call()
	{
		$to = $this->input->post('to');
		if(strlen($to) != 0)
		{
			echo __('Calling ') . $to . "<br />\n";
			$esl = new EslManager();
                        
			$from = '1000';
			if($esl)
			{
				//$esl->originate('1414XXX6920', '1000') . "\n";
				$esl->originateLocal($to, $from);
			} else {
				echo __('Call failed! ESL Failed') ."<br />\n";
			}
		} else {
			echo __('Enter a valid phone number') ."<br />\n";
		}
		die();
	}

	public function mobile()
	{
		$sms = Smsfactory::setDriver(Kohana::config('dash.sms_driver'));
		
		if($sms)
		{
			$login = Kohana::config('dash.sms_login');
			$password = Kohana::config('dash.sms_password');
			$api = Kohana::config('dash.sms_api');
			
			$to  = $this->input->post('to');
			$message = $this->input->post('message');
			
			echo __('Texting ') . $to;
			echo "<br>" . $message . "<br />";
			
			$sms->auth($login, $password, $api);
			$sms->send($to, $message);
			
			if(sizeof($sms->errorStack) > 0)
			{
				
				echo implode("<br />", $sms->errorStack);
			} else {
				echo __('Message Sent!');
			}
		} else {
			echo __('Fail to load SMS driver...');
		}
		
		die();
	}
	
	
	
	/* auto complete functions */
	
	private function formatAutoComplete($result, $type = '')
	{
		$output = '';
		foreach($result as $number)
		{
			$output .= sprintf("%s <%s@%s>",  $number['display_name'], $number['number'], $type) . "\n";
		}
		return $output;
	}
	
	/**
	 * This little schema design "over sight" could be solved with unions
	 * 
	 * 
	 */
	public function searchCallable()
	{
		$q = Doctrine_Query::create();
		$q->select('c.display_name, c.home_number AS number')->from('Contact c')->where(sprintf('user_id = %d', $_SESSION['user_id']))->andWhere("c.home_number  != ''");
		echo $this->formatAutoComplete($q->execute(array(), Doctrine::HYDRATE_ARRAY), 'home');

		$q = Doctrine_Query::create();
		$q->select('c.display_name, c.work_number AS number')->from('Contact c')->where(sprintf('user_id = %d', $_SESSION['user_id']))->andWhere("c.work_number  != ''");
		echo $this->formatAutoComplete($q->execute(array(), Doctrine::HYDRATE_ARRAY), 'work');

		$q = Doctrine_Query::create();
		$q->select('c.display_name, c.mobile_number AS number')->from('Contact c')->where(sprintf('user_id = %d', $_SESSION['user_id']))->andWhere("c.mobile_number  != ''");
		echo $this->formatAutoComplete($q->execute(array(), Doctrine::HYDRATE_ARRAY), 'mobile');
	
		die();
	}
	
	
	public function searchMobile()
	{
		$q = Doctrine_Query::create();
		$q->select('c.first_name, c.last_name, c.mobile_number AS number')->from('Contact c')->where(sprintf('user_id = %d', $_SESSION['user_id']))->andWhere("c.mobile_number  != ''");
		echo $this->formatAutoComplete($q->execute(array(), Doctrine::HYDRATE_ARRAY), 'mobile');
	
		die();
	}				
}

