<?php
class XmlcdrManager
{
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
		$cdr['start_stamp'] = date('o-m-d H:i:s', urldecode($xml_cdr->variables->start_epoch));
		$cdr['answer_stamp'] = date('o-m-d H:i:s', urldecode($xml_cdr->variables->answer_epoch));
		$cdr['end_stamp'] = date('o-m-d H:i:s', urldecode($xml_cdr->variables->end_epoch));
		$cdr['duration'] = (int)$xml_cdr->variables->duration;
		$cdr['billsec'] = (int)$xml_cdr->variables->billsec;
		$cdr['hangup_cause'] = (string)$xml_cdr->variables->hangup_cause;
		$cdr['uuid'] = (string)$xml_cdr->callflow[0]->caller_profile->uuid;
		$cdr['bleg_uuid'] = (string)$xml_cdr->variables->bridge_uuid;
		$cdr['accountcode'] = (string)$xml_cdr->variables->accountcode;
		$cdr['user_context'] = (string)$xml_cdr->variables->user_context;
		$cdr['read_codec'] = (string)$xml_cdr->variables->read_codec;
		$cdr['write_codec'] = (string)$xml_cdr->variables->write_codec;
		$cdr['dialed_domain'] = (string)$xml_cdr->variables->dialed_domain;
		$cdr['dialed_user'] = (string)$xml_cdr->variables->dialed_user;
		$cdr['direction'] = (string)$xml_cdr->variables->direction;
		$cdr['cdrrec'] = $xml;
		
		//Try to figure out the domain
		if (strlen((string)$xml_cdr->variables->domain_name) > 0)
		{
			$cdr['domain_name'] = (string)$xml_cdr->variables->domain_name;
		} 
		elseif (strlen((string)$xml_cdr->variables->dialed_domain) > 0)
		{
			$cdr['domain_name'] = (string)$xml_cdr->variables->dialed_domain;
		}
		else
		{	
			$context = Doctrine::getTable('Context')->findOneBy('context_id', (int) substr((string)$xml_cdr->variables->force_transfer_context, -1, 1));
			$cdr['domain_name'] = $context->Account->Location->domain;
		}

        try {
 	       	$xmlcdr = new Xmlcdr();
           	$xmlcdr->synchronizeWithArray($cdr);
			$xmlcdr->save();
            Kohana::log('info', 'Saving CDR Record');
        } catch(Exception $e) {
            Kohana::log('error', $e->getMessage());
        }
	}
	
	public static function buildcdrarray($cdrxml)
	{
        try  {
			$xmlarr = xml::xmlstrtoarr($cdrxml);
		} catch(Exception $e) {
			Kohana::log('error', $e->getMessage());
			return;
		}
		
		return arr::flatten($xmlarr);
	}
}
