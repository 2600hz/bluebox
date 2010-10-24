<?php
class XmlcdrManager
{
        public static function getAccounts()
	{

	}
	

	
	public static function addXMLCDR($xml)
	{

           

            try  {
			$xml_cdr = new SimpleXMLElement($xml);
		} catch(Exception $e) {
			Kohana::log('error', $e->getMessage());
			return;
		}

		$cdr = array();
		$cdr['caller_id_name'] = (string)$xml_cdr->callflow[0]->caller_profile->caller_id_name;
		$cdr['caller_id_number'] = (string)$xml_cdr->callflow[0]->caller_profile->caller_id_number;
		$cdr['destination_number'] = (string)$xml_cdr->callflow[0]->caller_profile->destination_number;
		$cdr['user_name'] = (string)$xml_cdr->callflow[0]->caller_profile->user_name;
		$cdr['context'] = (string)$xml_cdr->callflow[0]->caller_profile->context;
		$cdr['start_stamp'] = urldecode($xml_cdr->variables->start_stamp);
		$cdr['answer_stamp'] = urldecode($xml_cdr->variables->answer_stamp);
		$cdr['end_stamp'] = urldecode($xml_cdr->variables->end_stamp);
		$cdr['duration'] = (int)$xml_cdr->variables->duration;
		$cdr['billsec'] = (int)$xml_cdr->variables->billsec;
		$cdr['hangup_cause'] = (string)$xml_cdr->variables->hangup_cause;
		$cdr['uuid'] = (string)$xml_cdr->callflow[0]->caller_profile->uuid;
		$cdr['bleg_uuid'] = (string)$xml_cdr->variables->bridge_uuid;
		$cdr['accountcode'] = (string)$xml_cdr->variables->accountcode;
		$cdr['domain_name'] = (string)$xml_cdr->variables->domain_name;
		$cdr['user_context'] = (string)$xml_cdr->variables->user_context;
		$cdr['read_codec'] = (string)$xml_cdr->variables->read_codec;
		$cdr['write_codec'] = (string)$xml_cdr->variables->write_codec;
		$cdr['dialed_domain'] = (string)$xml_cdr->variables->dialed_domain;
		$cdr['dialed_user'] = (string)$xml_cdr->variables->dialed_user;
		


		
		//getDomain()
		if(strlen($cdr['domain_name']) == 0)
		{
			$domain = $cdr['dialed_domain'];
			$direction = 'in';
		} else {
			$domain = $cdr['domain_name'];
			$direction = 'out';
		}
		
		//fwrite($this->fp, "\n==direction: $direction \n");
		
		//$domain = str_replace('.', '_', trim($domain));
		$account = $cdr['accountcode'];
		
		$cdr['account'] = $account;
		//$cdr['domain'] = $domain;
		$cdr['direction'] = $direction;
		

		//$cdr['start'] = new MongoDate(strtotime($cdr['start_stamp']));

                try{
                    $xmlcdr = new Xmlcdr();
                    $xmlcdr->synchronizeWithArray($cdr);
                    $xmlcdr->save();
                     Kohana::log('info', 'Saving CDR Record');
                } catch(Exception $e) {
                    Kohana::log('error', $e->getMessage());

                }

		

		
	}
}
