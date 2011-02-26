<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Queue_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        $xml_root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

        $xml->setXmlRoot($xml_root . '/queues');

        $queue_root = '/queue[@name="queue_' . $base['queue_id'] . '"]';

        $xml->update($queue_root . '{@bluebox="' . $base['name'] . '"}');

        $moh = '$${hold_music}';

        $xml->update($queue_root . '/param[@name="moh-sound"]{@value="' . $moh . '"}');

        foreach($base['registry'] as $key => $val)
        {
            $key = preg_replace('/_/', '-', $key);

            $xml->update($queue_root . '/param[@name="' . $key . '"]{@value="' . $val . '"}');
        }

	$esl = EslManager::getInstance();

	if($esl->isConnected())
	{
		$esl->reloadxml();
		$esl->reload('mod_callcenter');
	}
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        $xml_root = '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]';

        $xml->setXmlRoot($xml_root . '/queues/queue[@name="queue_' . $base['queue_id'] . '"]');

        $xml->deleteNode();
    }

    public static function dialplan($number)
    {
	$xml = Telephony::getDriver()->xml;

	$destination = $number['Destination'];

	if($id = arr::get($destination, 'queue_id'))
	{
		$xml->update('/action[@application="callcenter"]{@data="queue_' . $id . '"}');
	}
    }
}
