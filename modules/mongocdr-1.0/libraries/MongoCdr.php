<?php
class MongoCdr extends Mongo
{
    private $xml_cdr;
    private $fp;
    
    public function __set($key, $value)
    {
		
	}
    
    /*
    public function connect($host = 'localhost', $port = 27017)
    {
	$this->fp = fopen('/tmp/mongocdr.txt', 'a+');

		try {
			$this->connection = new Mongo($host, $port); // connects to localhost:27017
		} catch(Exception $e) {
			echo '<p>Error connecting: ' . $e->getMessage() . '</p>';
			echo '<p>Error code: ' . $e->getCode() . '</p>';
		}
	}
	*/

	public function __construct()
	{
		$this->fp = fopen('/tmp/mongocdr.txt', 'a+');
		parent::__construct();
	}
	
	public function getAccounts()
	{
		$list = $this->listCollections();

		foreach ($list as $collection) 
		{
			echo $collection. '<br />';

		}	
	}
	

	
	public function addXMLCDR($xml)
	{
		try  {
			$this->xml_cdr = new SimpleXMLElement($xml);
		} catch(Exception $e) {
			Kohana::log('error', $e->getMessage());
			return;
		}

		$cdr = array();
		$cdr['caller_id_name'] = (string)$this->xml_cdr->callflow[0]->caller_profile->caller_id_name;
		$cdr['caller_id_number'] = (string)$this->xml_cdr->callflow[0]->caller_profile->caller_id_number;
		$cdr['destination_number'] = (string)$this->xml_cdr->callflow[0]->caller_profile->destination_number;
		$cdr['user_name'] = (string)$this->xml_cdr->callflow[0]->caller_profile->user_name;
		$cdr['context'] = (string)$this->xml_cdr->callflow[0]->caller_profile->context;
		$cdr['start_stamp'] = urldecode($this->xml_cdr->variables->start_stamp);
		$cdr['answer_stamp'] = urldecode($this->xml_cdr->variables->answer_stamp);
		$cdr['end_stamp'] = urldecode($this->xml_cdr->variables->end_stamp);
		$cdr['duration'] = (int)$this->xml_cdr->variables->duration;
		$cdr['billsec'] = (int)$this->xml_cdr->variables->billsec;
		$cdr['hangup_cause'] = (string)$this->xml_cdr->variables->hangup_cause;
		$cdr['uuid'] = (string)$this->xml_cdr->callflow[0]->caller_profile->uuid;
		$cdr['bleg_uuid'] = (string)$this->xml_cdr->callflow[0]->caller_profile->bleg_uuid;
		$cdr['accountcode'] = (string)$this->xml_cdr->variables->accountcode;
		$cdr['domain_name'] = (string)$this->xml_cdr->variables->domain_name;
		$cdr['user_context'] = (string)$this->xml_cdr->variables->user_context;
		$cdr['read_codec'] = (string)$this->xml_cdr->variables->read_codec;
		$cdr['write_codec'] = (string)$this->xml_cdr->variables->write_codec;
		$cdr['dialed_domain'] = (string)$this->xml_cdr->variables->dialed_domain;
		$cdr['dialed_user'] = (string)$this->xml_cdr->variables->dialed_user;
		
		
		foreach($cdr as $k => $p)
		{	
			fwrite($this->fp, "$k $p\n");
		}

		//fwrite($this->fp, $_POST['cdr'] );	

		
		//getDomain()
		if(strlen($cdr['domain_name']) == 0)
		{
			$domain = $cdr['dialed_domain'];
			$direction = 'in';
		} else {
			$domain = $cdr['domain_name'];
			$direction = 'out';
		}
		
		fwrite($this->fp, "\n==direction: $direction \n");
		
		$domain = str_replace('.', '_', trim($domain));
		$account = $cdr['accountcode'];
		
		$cdr['account'] = $account;
		$cdr['domain'] = $domain;
		$cdr['direction'] = $direction;
		

		$cdr['start'] = new MongoDate(strtotime($cdr['start_stamp']));

		
		fwrite($this->fp, "\n==domain: $domain \n");
		fwrite($this->fp, "== account: $account \n");		
		
		try {
			$db = $this->selectDB('cdr');
			$collection = $db->selectCollection('record');
			$collection->insert($cdr);
		} catch(Exception $e)
		{
			fwrite($this->fp, $e->getMessage() );
			echo $e->getMessage() . "\n";
		}
		

		
	}
}
