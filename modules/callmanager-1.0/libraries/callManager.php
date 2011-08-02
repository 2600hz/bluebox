<?php defined('SYSPATH') or die('No direct access allowed.');

class callManager {
	public static function processAction($funcname, &$controller, $params)
	{
		$callmanagementclass = strtolower(Telephony::getDriverName()) . '_callmanager_Driver';
		$callmanagementobject = new $callmanagementclass();
		return $callmanagementobject->processAction($funcname, $controller, $params);
	}

	public static function executeAction($funcname, $params)
	{
		$callmanagementclass = strtolower(Telephony::getDriverName()) . '_callmanager_Driver';
		$callmanagementobject = new $callmanagementclass();
		return $callmanagementobject->executeAction($funcname, $params);
	}

	public static function installDefaultFunctions()
	{
		try {
			callmanagerFunction::reregister(
					'hangup',
					'callmanager',
					'Hangup',
					'freeswitch.callstatus.active;',
					'Does this sound like a dial tone?',
			User::TYPE_SYSTEM_ADMIN
			);
		} catch (callmanagerException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
			throw $e;
		}

		try {
			callmanagerFunction::reregister(
					'hold',
					'callmanager',
					'Hold',
					'freeswitch.callstatus.active;',
					'Bring on the elevator music!',
			User::TYPE_SYSTEM_ADMIN
			);
		} catch (callmanagerException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
			throw $e;
		}

		try {
			callmanagerFunction::reregister(
					'monitor',
					'callmanager',
					'Monitor',
					'freeswitch.callstatus.active;',
					'Do you have a warrant?',
			User::TYPE_SYSTEM_ADMIN
			);
		} catch (callmanagerException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
			throw $e;
		}

		try {
			callmanagerFunction::reregister(
					'valetpark',
					'callmanager',
					'Park',
					'freeswitch.callstatus.active;',
					'As long as it\'s not parallel parking!!!',
			User::TYPE_SYSTEM_ADMIN
			);
		} catch (callmanagerException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
			throw $e;
		}

		try {
			callmanagerFunction::reregister(
					'record',
					'callmanager',
					'Record',
					'freeswitch.callstatus.active;',
					'For quality control purposes.',
			User::TYPE_SYSTEM_ADMIN
			);
		} catch (callmanagerException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
			throw $e;
		}

		try {
			callmanagerFunction::reregister(
					'transfer',
					'callmanager',
					'Transfer',
					'freeswitch.callstatus.active;',
					'Someone is on the phone for you...',
			User::TYPE_SYSTEM_ADMIN
			);
		} catch (callmanagerException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
			throw $e;
		}
	}

	public function __call($funcname, $params)
	{
		return self::executeAction($funcname, $params);
	}

	private function _throwError($errorMessage, $errorLevel = -10)
	{
		throw new callmanagerException($errorMessage, $errorLevel);
	}
}

?>