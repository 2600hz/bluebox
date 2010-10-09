<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Jon Blanton <rjdev943@gmail.com>
 * @author Rockwood Cataldo <rjdev943@gmail.com>
 * @license MPL
 * @package Xmpp
 */
class FreeSwitch_Xmpp_Driver extends FreeSwitch_Base_Driver
{

    public static function set($xmppData)
    {
        $xml = Telephony::getDriver()->xml;

        // Build the Dinagling profile...
        $root = '//document/section[@name="configuration"]/configuration[@name="dingaling.conf"]' .
                '/profile[@name="dingaling_' . $xmppData['xmpp_id'] . '"]';

        $xml->setXmlRoot($root);

        $xml->update('/param[@name="name"]{@value="' . $xmppData['name'] . '"}');

        $login = preg_replace('/\//', '\/', $xmppData['login']);
        $xml->update('/param[@name="login"]{@value="' . $login . '"}');

        $xml->update('/param[@name="password"]{@value="' . $xmppData['registry']['password'] . '"}');
        $xml->update('/param[@name="server"]{@value="' . $xmppData['registry']['loginserver'] . '"}');
        $xml->update('/param[@name="dialplan"]{@value="' . $xmppData['registry']['dialplan'] . '"}');
        $xml->update('/param[@name="message"]{@value="' . $xmppData['registry']['message'] . '"}');
        $xml->update('/param[@name="rtp-ip"]{@value="' . $xmppData['registry']['rtpip'] . '"}');
        $xml->update('/param[@name="auto-login"]{@value="' . ($xmppData['registry']['autologin'] == 1 ? 'true' : 'false') . '"}');
        $xml->update('/param[@name="auto-reply"]{@value="' . $xmppData['registry']['autoreply'] . '"}');
        $xml->update('/param[@name="sasl"]{@value="' . $xmppData['registry']['sasl'] . '"}');
        $xml->update('/param[@name="tls"]{@value="' . ($xmppData['registry']['tls'] == 1 ? 'true' : 'false') . '"}');
        $xml->update('/param[@name="use-rtp-timer"]{@value="' . $xmppData['registry']['usertptimer'] . '"}');
        $xml->update('/param[@name="vad"]{@value="' . $xmppData['registry']['vad'] . '"}');
        $xml->update('/param[@name="candidate-acl"]{@value="' . $xmppData['registry']['candidateacl'] . '"}');
        $xml->update('/param[@name="local-network-acl"]{@value="' . $xmppData['registry']['localnetacl'] . '"}');

        $xml->update('/param[@name="context"]{@value="context_' . $xmppData['registry']['inbound_context'] . '"}');
        // Now to locate the 'real' extension, our exten variable is holding the number's index
        $realExten = Doctrine::getTable('Number')->find($xmppData['registry']['exten']);
        $xml->update('/param[@name="exten"]{@value="' . $realExten['number'] . '"}');


        // Build the dialplan from the ...l
        foreach ($xmppData['registry']['patterns'] as $simple_route_id => $options)
        {
            foreach ($xmppData['registry']['contexts'] as $context_id => $enabled)
            {//dingaling_1_pattern_1
                $xml = FreeSwitch::createExtension($xmppData['xmpp_id'] .'_pattern_' .$simple_route_id, 'dingaling', 'context_' .$context_id);

                if (empty($enabled))
                {
                    $xml->deleteNode();

                    continue;
                }

                if (empty($options['enabled']))
                {
                    $xml->deleteNode();

                    continue;
                }

                if (!$pattern = simplerouter::getOutboundPattern($simple_route_id, 'freeswitch'))
                {
                    $xml->deleteNode();

                    continue;
                }

                $xml->deleteChildren();

                $condition = '/condition[@field="destination_number"][@expression="' .$pattern . '"][@bluebox="pattern_' .$simple_route_id .'"]';

                if (!empty($options['prepend']))
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="prepend"]{@data="prepend=' .$options['prepend'] . '"}');
                }
                else
                {
                    $xml->update($condition .'/action[@application="set"][@bluebox="prepend"]{@data="prepend="}');
                }
                
                $xml->update($condition . '/action[@application="set"][@data="hangup_after_bridge=true"]');
                $xml->update($condition . '/action[@application="bridge"]{@data="dingaling\/' . $xmppData['name'] . '\/+${prepend}$1@' . $xmppData['registry']['outboundserver'] . '"}');
            }
        }
    }

    public static function delete($xmppData)
    {
        //Delete dialplans
        foreach ($xmppData['registry']['patterns'] as $simple_route_id => $options)
        {
            foreach ($xmppData['registry']['contexts'] as $context_id => $enabled)
            {
                $xml = FreeSwitch::createExtension($xmppData['xmpp_id'] .'_pattern_' . $simple_route_id, 'dingaling', 'context_' .$context_id);

                $xml->deleteNode();
            }
        }

        //Delete jingle profile
        $xml = Telephony::getDriver()->xml;

        $root = '//document/section[@name="configuration"]/configuration[@name="dingaling.conf"]' .
                '/profile[@name="dingaling_' . $xmppData['xmpp_id'] . '"]';

        $xml->setXmlRoot($root);

        $xml->deleteNode();
    }
}
