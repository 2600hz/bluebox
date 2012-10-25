<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    SIP
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Asterisk_Sip_Driver extends Asterisk_Base_Driver
{
    /**
     * Set SIP device specific settings for Asterisk config file types
     */
    public static function set($base)
    {
        // Get the asterisk driver
        $doc = Telephony::getDriver()->doc;

        // If this is a new device add it accordingly
        if ($base instanceof Device) 
        {
            // if this is an update of an existing user, remove the old entry
            $modified = $base->getModified(TRUE, TRUE);

            if (!empty($modified['plugins']['sip']['username']))
            {
                if (empty($base['plugins']['sip']))
                {
                    return;
                }
                
                if ($modified['plugins']['sip']['username'] != $base['plugins']['sip']['username'])
                {
                    $doc->deleteContext('sip.conf', $modified['plugins']['sip']['username']);
                }
            }

            if (empty($base['plugins']['sip']))
            {
                return;
            }

            $sip = $base['plugins']['sip'];

            // Get the username of this sip device
            $section = $sip['username'];

            $doc->setPosition('sip.conf', $section);

            $doc->update('sip.conf', $section, 'type', 'friend');

            $doc->update('sip.conf', $section, 'secret', $sip['password']);

            $doc->update('sip.conf', $section, 'host', 'dynamic');

            $doc->update('sip.conf', $section, 'context', 'context_' .$base['context_id']);

            $doc->update('sip.conf', $section, 'subscribecontext', 'context_' .$base['context_id']);

        } 
        else if ($base instanceof Trunk)
        {
//            $section = 'trunk_' . $base->trunk_id;
//            $domain = 'context_' .$base->context_id;
//
//            $register = '';
//            if (!empty($obj->username)) {
//                $doc->update('sip.conf', $section, 'fromuser', $obj->username);
//                $doc->update('sip.conf', $section, 'username', $obj->username);
//                $register .= $obj->username;
//            }
//
//            if (!empty($obj->password)) {
//                $doc->update('sip.conf', $section, 'secret', $obj->password);
//                $register .= ':' .$obj->password;
//            }
//
//            if (!empty($register)) {
//                $register .= '@' .$base->server;
//
//                // Add support for custom contact header
//                if ($obj->contact) {
//                    $register .= '/' . $obj->contact;
//                }
//
//                $register = '>' .$register;
//            } else {
//                $register = FALSE;
//            }
//
//            $oldValues = $obj->getModified(TRUE, TRUE);
//            $oldRegister = '';
//            if (!empty($oldValues['username']))
//                $oldRegister .= $oldValues['username'];
//            else
//                $oldRegister .= $obj->username;
//            if (!empty($oldValues['password']))
//                $oldRegister .= ':' .$oldValues['password'];
//            else
//                $oldRegister .= ':' .$obj->password;
//            if (!empty($oldValues['server']))
//                $oldRegister .= $oldValues['server'];
//            else
//                $oldRegister .= '@' .$base->server;
//
//            $doc->update('sip.conf', 'general', 'register', $oldRegister, $register);
//
//            $doc->setPosition('sip.conf', $section);
//            $doc->update('sip.conf', $section, 'type', 'friend');
//            $doc->update('sip.conf', $section, 'host', $base->server);
//            $doc->update('sip.conf', $section, 'insecure', 'invite,port');  // Allow unauthenticated inbound calls
//            $doc->update('sip.conf', $section, 'canreinvite', 'no');
//            $doc->update('sip.conf', $section, 'context', $domain);
        }
    }

    public static function delete($base)
    {
        if (empty($base['plugins']['sip']))
        {
            return;
        }

        $sip = $base['plugins']['sip'];

        // get the section name, depending on the type
        if ($base instanceof SipDevice)
        {
            // Get the username of this sip device
            $section = $sip['username'];
        }
        elseif ($base instanceof Trunk)
        {
//            $sectionName = 'trunk_' . $base->trunk_id;
//
//            $register = '';
//            if (!empty($obj->username)) {
//                $register .= $obj->username;
//            }
//
//            if (!empty($obj->password)) {
//                $register .= ':' .$obj->password;
//            }
//
//            if (!empty($register)) {
//                $register .= '@' .$base->server;
//                $doc->update('sip.conf', 'general', 'register', $register, FALSE);
//            }
        }
        else
        {
            return FALSE;
        }

        // Get the asterisk driver
        $doc = Telephony::getDriver()->doc;

        $doc->deleteContext('sip.conf', $section);
    }
}