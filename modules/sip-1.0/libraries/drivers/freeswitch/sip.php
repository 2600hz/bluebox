<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Sip_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        if ($base instanceof Device)
        {
            if (empty($base['plugins']['sip']))
            {
                return;
            }

            $sip = $base['plugins']['sip'];

            $domain = '$${location_' .$base['User']['location_id'] .'}';

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            $xml->setAttributeValue('', 'id', $sip['username']);

            $xml->update('/params/param[@name="password"]{@value="' .$sip['password'] .'"}');

            $xml->update('/params/param[@name="dial-string"]{@value="' . '{presence_id=${dialed_user}@${dialed_domain}}${sofia_contact(${dialed_user}@${dialed_domain})}"}');

            if ($base['context_id'] > 0)
            {
                $xml->update('/variables/variable[@name="user_context"]{@value="context_' .$base['context_id'] . '"}');

                 // In most cases, the call transfer context should match the default context
                $xml->update('/variables/variable[@name="force_transfer_context"]{@value="context_' .$base['context_id'] . '"}');
            }
            else
            {
                $xml->deleteNode('/variables/variable[@name="user_context"]');

                $xml->deleteNode('/variables/variable[@name="force_transfer_context"]');
            }

            $xml->update('/variables/variable[@name="user_originated"]{@value="true"}');

            $xml->update('/variables/variable[@name="toll_allow"]{@value="domestic"}');

            $xml->update('/variables/variable[@name="accountcode"]{@value="' .$sip['username'] .'"}');
        }
        else if ($base instanceof Trunk)
        {
            $plugins = $base['plugins'];

            if (!empty($plugins['sipinterface']['sipinterface_id']))
            {
                $xml = FreeSwitch::setSection('gateway', 'sipinterface_' . $plugins['sipinterface']['sipinterface_id'], 'trunk_' . $base['trunk_id']);

                if (!empty($plugins['sip']['username']))
                {
                    $xml->update('/param[@name="username"]{@value="' .$plugins['sip']['username'] .'"}');

                    if (!empty($plugins['sip']['password']))
                    {
                        $xml->update('/param[@name="password"]{@value="' .$plugins['sip']['password'] .'"}');
                    }

                    // Assume we want to register if user/pass is specified
                    // TODO: Make this a checkbox?
                    $xml->update('/param[@name="register"]{@value="true"}');
                } 
                else
                {
                    // Assume we don't want to register if no user/pass is specified
                    $xml->update('/param[@name="register"]{@value="false"}');

                    // Have to put something, so we'll just make it generic
                    $xml->update('/param[@name="username"]{@value="TCAPI_User"}');
                    
                    $xml->update('/param[@name="password"]{@value="TCAPI_User"}');
                }

                // Route calls with no specific DID info to an inbound number
                if (!empty($plugins['sip']['inbound'])) {
                    $xml->update('/param[@name="extension"]{@value="' .$plugins['sip']['inbound'] .'"}');
                } else {
                    $xml->deleteNode('/param[@name="extension"]');
                }

                // Force a specific contact username in the contact header
                if(!empty($plugins['sip']['contact']))
                {
                    $xml->update('/param[@name="extension-in-contact"]{@value="' . $plugins['sip']['contact'] . '"}');
                }
                else
                {
                    $xml->deleteNode('/param[@name="extension-in-contact"]');
                }

                // Add auto_to_user support, allowing DID to be in a different spot
                if (!empty($plugins['sip']['to_user'])) {
                    $xml->update('/param[@name="auto_to_user"]{@value="true"}');
                }
                else
                {
                    $xml->deleteNode('/param[@name="auto_to_user"]');
                }
            }
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
        else if ($base instanceof Trunk)
        {
            $plugins = $base['plugins'];

            if (!empty($plugins['sipinterface']['sipinterface_id']))
            {
                $xml = FreeSwitch::setSection('gateway', 'sipinterface_' . $plugins['sipinterface']['sipinterface_id'], 'trunk_' . $base['trunk_id']);

                $xml->deleteNode();
            }
        }
    }
}