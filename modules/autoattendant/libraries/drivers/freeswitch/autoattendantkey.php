<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_AutoAttendantKey_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this AutoAttendant
     */
    public static function set($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $xml->setXmlRoot('//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]/menus');
        
        $greeting = ($obj->AutoAttendant->type == 'audio') ? $greeting = str_replace('/', '\/', FileManager::getFilePath($obj->AutoAttendant->file_id)) : 'say: ' . $obj->AutoAttendant->tts_string;
		
        $node = sprintf('/menu[@name="%s"]{@timeout="%d"}{@inter-digit-timeout="%d"}{@greet-long="%s"}{@greet-short="%s"}', 
					'auto_attendant_' . $obj->AutoAttendant->auto_attendant_id,
					$obj->AutoAttendant->timeout * 1000,
					$obj->AutoAttendant->digit_timeout * 1000,
					$greeting,
					$greeting);

       if (!empty($obj->AutoAttendant->extension_context_id)) {
           $node .= '{@digit-len="' .$obj->AutoAttendant->extension_digits .'"}';
       }

        if (empty($obj->auto_attendant_key) && $obj->auto_attendant_key !== 0) return;

        $key = $obj->auto_attendant_key;
        $key = ($key === 'asterisk') ? '*' : $key;   
        $key = ($key === 'pound') ? '#' : $key;
	
        Kohana::log('debug', 'Setting xml for Auto Attendant ' . $obj->AutoAttendant->auto_attendant_id . ', key ' . $obj->auto_attendant_key);

        $transferTo = dialplan::getTransfer($obj->number_id);
        $xml->update($node . sprintf('/entry[@action="menu-exec-app"][@digits="%s"][@param="%s"]', $key, $transferTo));
    }

    public static function delete($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $xml->setXmlRoot(sprintf('//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]/menus/menu[@name="%s"]', 'auto_attendant_' . $obj->auto_attendant_id));

        $key = $obj->auto_attendant_key;
        $key = ($key === 'asterisk') ? '*' : $key;   
        $key = ($key === 'pound') ? '#' : $key;

        $xml->deleteNode(sprintf('/entry[@action="menu-exec-app"][@digits="%s"]', $key));
    }
}
