<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_PagingGroup_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj) 
    {

    }

    public static function delete($obj) 
    {

    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];

        $xmlText = '        
        <action application="set" data="api_hangup_hook=conference ' . $destination['pgg_id'] . ' kick all"/>
        <action application="answer"/>
        <action application="export" data="sip_invite_params=intercom=true"/>
        <action application="export"><![CDATA[alert_info=<sip:$${C}>;Ring;Answer]]></action>
        <action application="export"><![CDATA[sip_h_Call-Info=<sip:$${location_' . $number->location_id . '}>;answer-after=0]]></action>
        <action application="set" data="conference_auto_outcall_caller_id_name=${effective_caller_id_name}"/>
        <action application="set" data="conference_auto_outcall_caller_id_number=${effective_caller_id_number}"/>
        <action application="set" data="conference_auto_outcall_timeout=5"/>
        ';

        foreach ($destination['pgg_device_ids'] as $deviceid)
        {
        	$deviceobj = Doctrine::getTable('Device')->FindOneBy('device_id', $deviceid);
        	Kohana::log('debug', print_r($deviceobj['registry'], true));
        	$xmlText .= '<action application="conference_set_auto_outcall" data="user/' . $deviceobj->plugins['sip']['username'] . '@$${location_' . $deviceobj->User->location_id . '}"/>
	';
        }
        $conftype = ($destination['pgg_type'] == 'page' ? 'Paging' : 'Intercom');
        $confobj = Doctrine::getTable('Conference')->findOneByName($conftype);
        $xmlText .= '<action application="conference" data="' . $destination['pgg_id'] . '@conference_' . $confobj['conference_id'] .'"/>
        <action application="conference" data="' . $destination['pgg_id'] . ' kick all"/>
        ';
        $xml->replaceWithXml($xmlText);
    }
}
?>