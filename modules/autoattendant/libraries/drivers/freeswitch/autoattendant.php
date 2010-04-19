<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_AutoAttendant_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this Auto Attendant
     */
    public static function set($obj)
    {

	$xml = Telephony::getDriver()->xml;

        $xml->setXmlRoot('//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]/menus');
        
        $greeting = ($obj->type == 'audio') ? str_replace('/', '\/', FileManager::getFilePath($obj->file_id)) : 'say: ' . $obj->tts_string;

        $node = sprintf('/menu[@name="%s"]{@timeout="%d"}{@inter-digit-timeout="%s"}{@greet-long="%s"}{@greet-short="%s"}',
					'auto_attendant_' . $obj->auto_attendant_id,
					$obj->timeout * 1000,
                                        $obj->digit_timeout * 1000,
					$greeting,
					$greeting);
                                    
       if (!empty($obj->extension_context_id)) {
           $node .= '{@digit-len="' .$obj->extension_digits .'"}';
       }

       $xml->update($node); // work from this location

       $xml->setXmlRoot(sprintf('//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]/menus/menu[@name="%s"]', 'auto_attendant_' . $obj->auto_attendant_id));
       $xml->deleteChildren();

       if (!empty($obj->extension_context_id)) {
           $xml->update(sprintf('/entry[@action="menu-exec-app"][@name="catch_all"][@digits="\/^([0-9]{%s})$\/"][@param="execute_extension $1 XML context_%s"]', $obj->extension_digits, $obj->extension_context_id));
       }
    }

    public static function delete($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $xml->setXmlRoot(sprintf('//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]/menus/menu[@name="%s"]', 'auto_attendant_' . $obj->auto_attendant_id));

        $xml->deleteNode();
    }

    /**
     * Add Auto Attendant to the dialplan
     * @param FreePbx_Record $obj
     *
     * @author Dale Hege
     */
    public static function dialplan($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $xml->update('/action[@application="answer"]');
        $xml->update('/action[@application="ivr"]{@data="auto_attendant_' . $obj->AutoAttendant->auto_attendant_id .'"}');
    }
}
