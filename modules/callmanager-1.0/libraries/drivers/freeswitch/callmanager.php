<?php defined('SYSPATH') or die('No direct access allowed.');

class Freeswitch_callmanager_Driver extends callmanager_Driver {
		
		public static $eslManager = null;
	
		public static $summaryfields = array(
			'direction' => 'In/Out',
			'state' => 'State',
			'callstate' => 'Call State',
			'cid_name' => 'Caller Name',
			'cid_num' => 'Caller Number',
			'dest' => 'Destination',
			'application' => 'Application',
			'application_data' => 'App Data'
		);
	
		public static $detailfields = array(
			'Channel Info' => array(
				'Unique-ID' => 'ID',
				'Caller-Dialplan' => 'Dial Plan',
				'Caller-Context' => 'Context',
				'Caller-Source' => 'Source App'
			),
			'Caller' => array(
				'variable_remote_media_ip' => 'Net Addr',
				'variable_remote_media_port' => 'Port'
			),
			'Times' => array(
				'Caller-Channel-Created-Time' => 'Channel Start',
				'Caller-Channel-Answered-Time' => 'Answered',
				'Caller-Channel-Hangup-Time' => 'Hangup',
				'Caller-Channel-Transfer-Time' => 'Transfer'
			),
			'Media' => array(
				'variable_read_codec' => 'Read Codec',
				'variable_read_rate' => 'Read Bitrate',
				'variable_write_codec' => 'Write Codec',
				'variable_write_rate' => 'Write Rate'
			)
		);
	
		public static $detaildatetimefields = array(
			'Event-Date-Timestamp',
			'Caller-Profile-Created-Time',
			'Caller-Channel-Created-Time',
			'Caller-Channel-Answered-Time',
			'Caller-Channel-Progress-Time',
			'Caller-Channel-Progress-Media-Time',
			'Caller-Channel-Hangup-Time',
			'Caller-Channel-Transfer-Time'
		);
		
		public static function _initEslManager()
		{
			self::$eslManager = new EslManager();
		}
		
		public static function getESLManager()
		{
			if (self::$eslManager == null)
				self::_initEslManager();
			return self::$eslManager;
		}
		
		public static function getESL()
		{
			return self::getESLManager()->getESL();
		}
		
		public function getFunctionsForCall($callinfo)
		{
			$contextList = '';
			if (isset($callinfo['state']))
				switch ($callinfo['state']) {
					case 'CS_NEW':
					case 'CS_INIT':
						$contextList .= 'freeswitch.callstatus.setup;';
						break;
					case 'CS_ROUTING':
					case 'CS_SOFT_EXECUTE':
					case 'CS_EXECUTE':
					case 'CS_EXCHANGE_MEDIA':
					case 'CS_PARK':
						$contextList .= 'freeswitch.callstatus.active;';
						break;
					case 'CS_CONSUME_MEDIA':
					case 'CS_HIBERNATE':
					case 'CS_RESET':
					case 'CS_HANGUP':
					case 'CS_REPORTING':
					case 'CS_DESTROY':
						$contextList .= 'freeswitch.callstatus.teardown;';
						break;
				}
			if (isset($callinfo['callstate']) && ($callinfo['callstate'] == 'ACTIVE'))
				$contextList .= 'freeswitch.callstatus.active;';
					
			return callmanagerFunction::getFunctionsByContext($contextList);
		}
		
		public function getIDField()
		{
			return 'uuid';
		}
		
		public function getSummaryFields()
		{
			return freeswitch_callmanager_Driver::$summaryfields;
		}
		
		public function getDetailFields()
		{
			return freeswitch_callmanager_Driver::$detailfields;
		}
		
		public function getDetailDatetimeFields()
		{
			return freeswitch_callmanager_Driver::$detaildatetimefields;
		}
}
?>