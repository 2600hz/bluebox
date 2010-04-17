<?php

class RosettaManager
{

	private $langpair;
	private $from = 'en'; 
	private $to = 'es';
	private $version = '1.0';
	private $base = 'http://ajax.googleapis.com/ajax/services/language/translate?';
	private $ch;
    private static $instance;
    
	public function __destruct()
	{
		if($this->ch)
		{
			curl_close($this->ch);
		}
	}

    public static function instance() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    // Prevent users to clone the instance
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
	
	
	//'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=hello%20world&langpair=en%7Cit'
	/**
	 * Returns translated string upon success, false on failure
	 */
	public function translate($string)
	{
		
		
		$translation = Doctrine::getTable('Rosetta')->findOneByRefString(array($string));
		
		if($translation)
		{
			/* check to see if we can hit cache */
			$cacheCheck = Doctrine_Query::create()
			->select('t.trans_string, r.ref_string')
			->from('Rosetta r, r.Translation t')
			->where('t.lang = ? AND r.ref_string = ?', array($this->to, $string))->limit(1)
			->execute(array(), Doctrine::HYDRATE_SCALAR);	
			if(sizeof($cacheCheck) > 0)
			{
				// put memory caching here.
				return $cacheCheck[0]['t_trans_string']; //clean this up.
			} else { 
				$lookup = $this->lookup($string);	
				$translation->Translation[$this->to]->trans_string = $lookup;
				$translation->save();
			}
			
			return $lookup;	
		} else {
			$lookup = $this->lookup($string);
			$translation = new Rosetta();
			$translation->ref_string = $string;
			$translation->Translation[$this->to]->trans_string = $lookup;
			$translation->save();
			return $lookup;
			
		}

	}

	public function lookup($string)
	{
		if(!$this->ch)
		{
			$this->ch = curl_init(); //try to make a connection if one does not exist
		}
		
		$query = array();
		
		$query['v'] = $this->version;
		$query['q'] = $string;
		$query['langpair'] = $this->from . '|' . $this->to;


		$result  = http_build_query($query); 
	
		// set URL and other appropriate options
		curl_setopt($this->ch, CURLOPT_URL, sprintf("%s%s", $this->base, $result));
		curl_setopt($this->ch, CURLOPT_HEADER, 0);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);


		$response = curl_exec($this->ch);
	
		if($response)
		{
			$result=json_decode($response, FALSE); //some strangenes here. It's supposed to retun NULL when it can't decode. Instead it was returned the html response!
			if(is_null($result->responseData->translatedText)) 
			{
				return false;
			} else {
			return  $result->responseData->translatedText;
			}
			
		} else {
			return false;
		}
	}
	
	public function setTo($lang)
	{
		$this->to = $lang;
		return self::$instance;
	}
	/*
	public function setFrom($lang)
	{
		$this->from = $lang;
		return self::$instance;
	}
	*/
}
