<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SipEncryption_Driver extends FreeSwitch_Base_Driver
{
    public static function conditioning()
    {
		Kohana::log('debug', 'preRoute');
    	$xml = FreeSWITCH::createExtension('encryption');
        $condition = '/condition[@field="${sip_has_crypto}"][@expression="^(AES_CM_128_HMAC_SHA1_32|AES_CM_128_HMAC_SHA1_80)$"][@break="never"]';
        $xml->update($condition . '/action[@application="set"][@data="sip_secure_media=true"]');
        $xml->update($condition . '/action[@application="export"][@data="sip_secure_media=true"]');
	}
	
    public static function set($base)
    {
        if ($base instanceof SipInterface)
        {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' . $base['sipinterface_id']);
            $xml->update('/settings/param[@name="tls"]{@value="' . (isset($base['plugins']['sipencryption']['enable']) && $base['plugins']['sipencryption']['enable'] == 'TRUE' ? 'TRUE' : 'FALSE') . '"}');
			if  ($base['plugins']['sipencryption']['type'] == 'tlsv1')
				$xml->update('/settings/param[@name="tls-bind-params"]{@value="transport=tls"}');
			else
				$xml->update('/settings/param[@name="tls-bind-params"]{@value=""}');
            $xml->update('/settings/param[@name="tls-sip-port"]{@value="' . $base['plugins']['sipencryption']['port'] . '"}');
            $xml->update('/settings/param[@name="tls-cert-dir"]{@value="' . str_replace('/', '\/', $base['plugins']['sipencryption']['certdir']) . '"}');
            $xml->update('/settings/param[@name="tls-version"]{@value="' . $base['plugins']['sipencryption']['type'] . '"}');
        }
    }

    public static function delete($base)
    {
    }
}
