<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_TierAgent_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        $xml_root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

        $xml->setXmlRoot($xml_root . '/tiers');

        $tier_xml = '/tier[@bluebox="tier_agent_' . $base['tier_agent_id'] . '"]';

        $tier_xml .= '{@queue="queue_' . $base['Tier']['queue_id'] . '"}{@agent="agent_' . $base['agent_id'] . '"}';

        $tier_xml .= '{@level="' . $base['Tier']['level'] . '"}{@position="' . $base['position'] . '"}';

        $xml->update($tier_xml);
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        $xml_root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

        $xml->setXmlRoot($xml_root . '/tiers/tier[@bluebox="tier_agent_' . $base['tier_agent_id'] . '"]');

        $xml->deleteNode();
    }
}
