<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_dbndir_Driver extends FreeSwitch_Base_Driver
{
	public static function set($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$root = '//document/section[@name="configuration"]/configuration[@name="directory.conf"][@description="Directory"]';
		$xml->setXmlRoot($root);

		$updatestr = $root . '/settings';
		$xml->update($updatestr);
		$root = '//document/section[@name="configuration"]/configuration[@name="directory.conf"][@description="Directory"]/profiles/';
		$xml->setXmlRoot($root);

		$updatestr = '/profile[@name="' . $ccData->dbn_profile . '"]';
		
		if (isset($ccData->cca_status) && !empty($ccData->cca_status) && $ccData->cca_status != '')
				$updatestr .= '{@status="' . $ccData->cca_status . '"}';

		if (isset($ccData->cca_max_no_answer) && !empty($ccData->cca_max_no_answer) && $ccData->cca_max_no_answer != '')
				$updatestr .= '{@max-no-answer="' . $ccData->cca_max_no_answer . '"}';

		if (isset($ccData->cca_wrap_up_time) && !empty($ccData->cca_wrap_up_time) && $ccData->cca_wrap_up_time != '')
				$updatestr .= '{@wrap-up-time="' . $ccData->cca_wrap_up_time . '"}';

		if (isset($ccData->cca_reject_delay_time) && !empty($ccData->cca_reject_delay_time) && $ccData->cca_reject_delay_time != '')
				$updatestr .= '{@reject-delay-time="' . $ccData->cca_reject_delay_time . '"}';

		if (isset($ccData->cca_busy_delay_time) && !empty($ccData->cca_busy_delay_time) && $ccData->cca_busy_delay_time != '')
				$updatestr .= '{@busy-delay-time="' . $ccData->cca_busy_delay_time . '"}';
		//$xml->update($updatestr);
	}

	public static function delete($ccData)
	{
		$xml = Telephony::getDriver()->xml;
		$locationRec = Doctrine::getTable('Location')->find($ccData->cca_locationid);
		$root = '//document/section[@name="configuration"]/configuration[@name="directory.conf"][@description="Directory"]';
		$xml->setXmlRoot($root);
		//$xml->deleteNode();
	}
	
	public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];
        $xmlText = '
    <action application="set" data="res=${callcenter_config(agent set status ${caller_id_number}@${domain_name} \'Available\')}" />
    <action application="answer" data=""/>
    <action application="sleep" data="500"/>
    <action application="playback" data="ivr/ivr-you_are_now_logged_in.wav"/>
    <action application="hangup" data=""/>
  ';
        $xml->replaceWithXml($xmlText);
	}
	
    public static function conditioning()
    {

        $xml = FreeSWITCH::createExtension('callrecord');

        // We'll always set the params for recorded calls, regardless of if they ever get used.
        $content = <<<XML

      <condition>
        <action application="set" data="RECORD_TITLE=Recording \${destination_number} \${caller_id_number} \${strftime(%Y-%m-%d %H:%M)}"/>
        <action application="set" data="RECORD_COPYRIGHT=(c) 2010 VoIP, Inc."/>
        <action application="set" data="RECORD_SOFTWARE=blue.box"/>
        <action application="set" data="RECORD_ARTIST=2600hz"/>
        <action application="set" data="RECORD_COMMENT=Automatically recorded via FreeSWITCH with blue.box"/>
        <action application="set" data="RECORD_DATE=\${strftime(%Y-%m-%d %H:%M)}"/>
        <action application="set" data="RECORD_STEREO=true"/>
      </condition>

XML;

        $xml->replaceWithXml($content);
    }

    public static function preNumber()
    {
        $number = Event::$data;
        $xml = Telephony::getDriver()->xml;

        if (isset($number['Destination']['plugins']['directory'])) {
			$directory = $number['Destination']['plugins']['directory'];
		} else {
			return;
		}
                
        if (!$callrecord)
        {
            return;
        }

        // Is this a trunk? If so, add generic stuff to see if this is a
        if ($number['Destination'] instanceof Device)
        	// Is inbound call recording set? If so, record calls to this user
	        if (!empty($callrecord['inbound']) and ($callrecord['inbound']))
	        {
	            $filename = Kohana::config('freeswitch.audio_root') . '/../recordings/${uuid}.wav';
	            $cr_cmd = str_replace('/', '\/', $filename);
	
	            //Not the prettiest fix, but it works
	            $condlog = '${cond(${callrecord_outbound} == 1 ? Already recording - Skipping inbound recording : Currently not recording, starting to record}';
	            $cond = '${cond(${callrecord_outbound} == 1 ?  : ' . $cr_cmd . '}';
	            $xml->update('/action[@application="log"][@bluebox="callrecord_action"][@data="INFO ' . $condlog . '"]');
	            $xml->update('/action[@application="record_session"][@bluebox="callrecord_action"][@data="' . $cond . '"]');
	        }
	        else
	       {
	            $xml->deleteNode('/action[@application="set"][@bluebox="callrecord_action"]');
	        }
    }
}
