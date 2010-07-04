<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Sip_Driver extends FreeSwitch_Base_Driver {

    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if (empty($base['plugins']['sip']))
        {
            return;
        }

        $sip = $base['plugins']['sip'];

        if ($base instanceof Device)
        {
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->setAttributeValue('', 'id', $sip['username']);

            $xml->update('/params/param[@name="password"]{@value="' .$sip['password'] .'"}');

            $xml->update('/params/param[@name="dial-string"]{@value="' . '{presence_id=${dialed_user}@${dialed_domain}}${sofia_contact(${dialed_user}@${dialed_domain})}"}');

            $xml->update('/variables/variable[@name="toll_allow"]{@value="domestic"}');

            $xml->update('/variables/variable[@name="accountcode"]{@value="' .$sip['username'] .'"}');
        }
    }

    public static function delete($base)
    {
        if ($base instanceof Device)
        {        
            $domain = '$${location_' .$base['User']['location_id'] .'}';

            $xml = FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->deleteNode();
        }
    }
}
