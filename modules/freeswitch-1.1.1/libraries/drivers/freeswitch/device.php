<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Device_Driver extends FreeSwitch_Base_Driver
{
    public static function set($device)
    {
        
    }

    public static function delete($device)
    {
        
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        if ($destination instanceof Device)
        {
            $domain = '$${location_' .$destination['User']['location_id'] .'}';

            $numberOptions = fs::getNumberOptions($number);

            $xml->update('/action[@application="set"][@bluebox="settingTimeout"]{@data="call_timeout=' .$numberOptions['timeout'] .'"}');

            $xml->update('/action[@application="set"][@bluebox="settingRing"]{@data="ringback=' . $numberOptions['ringtype'] . '"}');

            $xml->update('/action[@application="set"][@bluebox="settingRingXfer"]{@data="transfer_ringback=' . $numberOptions['ringtype'] . '"}');

            if (!empty($destination['plugins']['callerid']['internal_name']))
            {
                $xml->update('/action[@application="export"][@bluebox="sipCalleeIdName"]{@data="sip_callee_id_name=' .$destination['plugins']['callerid']['internal_name'] .'"}');
            }
            else
            {
                $xml->update('/action[@application="export"][@bluebox="sipCalleeIdName"]{@data="sip_callee_id_name=' .$destination['name'] .'"}');
            }
            
            $xml->update('/action[@application="export"][@bluebox="sipCalleeIdNumber"]{@data="sip_callee_id_number=' .$number['number'] .'"}');

            if (arr::get($number, 'registry', 'enable_encryption'))
	        {
	        	$condition = '/condition[@field="${sip_has_crypto}"][@expression="^(AES_CM_128_HMAC_SHA1_32|AES_CM_128_HMAC_SHA1_80)$"][@break="never"]';
	        	$xml->update($condition.'/action[@application="export"]{@data="sip_secure_media=true"}');
	        }
            
//            if(arr::get($destination, 'plugins', 'sip', 'sip_invite_format') == Sip_Plugin::SIP_FORMAT_DIGITS)
//            {
//                $xml->update('/action[@application="export"][@bluebox="settingXmlInviteFormat"]{@data="sip_invite_req_uri=sip:' .$number['number'] .'${regex(${sofia_contact(' . $destination['plugins']['sip']['username'] .'\@$${location_1})}|(\@.*)|$1)}"}');
//            }
//            else if(arr::get($destination, 'plugins', 'sip', 'sip_invite_format') == Sip_Plugin::SIP_FORMAT_E164)
//            {
//                if (!$extNumber = arr::get($destination, 'plugins', 'callerid', 'external_number'))
//                {
//                    $extNumber = $number['number'];
//                }
//
//                preg_match('/^\+?1?([2-9][0-8][0-9][2-9][0-9][0-9][0-9]{4})$/', $extNumber, $matches);
//
//                if (!empty($matches[1]))
//                {
//                    $extNumber = '1' .$matches[1];
//                }
//
//                $xml->update('/action[@application="export"][@bluebox="settingXmlInviteFormat"]{@data="sip_invite_req_uri=sip:+' .$extNumber .'${regex(${sofia_contact(' . $destination['plugins']['sip']['username'] .'\@$${location_1})}|(\@.*)|$1)}"}');
//            }
//            else
//            {
//                $xml->deleteNode('/action[@application="export"][@bluebox="settingXmlInviteFormat"]');
//            }

            $condition = '/condition[@field="${privacy_hide_number}"][@expression="false"][@break="never"]';

            $extenRoot = $xml->getExtensionRoot();

	    //If allowing multi sip interfaces, then we need to change the @data field with sofia_contact.
	    $dataString = 'user\/' .$destination['plugins']['sip']['username'] .'@' .$domain;
	    
	    if(arr::get($destination, 'registry', 'multi_sipinterface'))
	    {
		$dataString =  '${sofia_contact(*\/' .$destination['plugins']['sip']['username'] .'@' .$domain .')}';
	    }

            if (arr::get($destination, 'registry', 'anonymous_reject'))
            {
                $xml->setXmlRoot($extenRoot .$condition);
                
		//$xml->update('/action[@application="bridge"]{@data="user\/' .$destination['plugins']['sip']['username'] .'@' .$domain .'"}');
                $xml->update('/action[@application="bridge"]{@data="'.$dataString.'"}');

                $xml->setXmlRoot($extenRoot .'/condition[@bluebox="no_answer"]');
            }
            else
            {
		//$xml->update('/action[@application="bridge"]{@data="user\/' .$destination['plugins']['sip']['username'] .'@' .$domain .'"}');
                $xml->update('/action[@application="bridge"]{@data="'.$dataString.'"}');
	
                $xml->deleteNode($extenRoot .$condition);

                $xml->deleteNode($extenRoot .'/condition[@bluebox="no_answer"]');
            }
        }
    }
}
