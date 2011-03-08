<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Agent_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        $xml_root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

        $xml->setXmlRoot($xml_root . '/agents');

        $agent_xml = '/agent[@name="agent_' . $base['agent_id'] . '"]{@bluebox="' . $base['name'] . '"}{@type="' . $base['type'] . '"}';

        $location_id = $base['Device']['User']['location_id'];

        $username = $base['Device']['plugins']['sip']['username'];

        $agent_xml .= '{@contact="user\/' . $username . '@$${location_' . $location_id . '}"}';

        foreach($base['registry'] as $key => $val)
        {
            $key = preg_replace('/_/', '-', $key);

            $agent_xml .= '{@' . $key . '="' . $val . '"}';
        }

        $xml->update($agent_xml);
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        $xml_root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

        $xml->setXmlRoot($xml_root . '/agents/agent[@name="agent_' . $base['agent_id'] . '"]');

        $xml->deleteNode();
    }
}
