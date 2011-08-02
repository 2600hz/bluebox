<?php
class callmanager_Driver {
	public function getSummaryFields()
	{
		return array();
	}

	public function getSummaryDatetimeFields()
	{
		return array();
	}

	public function getDetailFields()
	{
		return array();
	}

	public function getDetailDatetimeFields()
	{
		return array();
	}
	
	public function getIDField()
	{
		throw new callmanagerException('Required function getIDField has not been implemented for driver ' . Telephony::getDriverName(), -10);
	}
	
	public function getFunctionsForCall($call)
	{
		return array();
	}
	
	public function getFunctionLink($function, $params, $linktype)
	{
		$functiondriverclass = Telephony::getDriverName() . '_cm' . strtolower($function) . '_Driver';
		if (!class_exists($functiondriverclass))
			throw new callmanagerException($function . ' is not implemented for ' . Telephony::getDriverName());
		return $functiondriverclass::getActionLink($params, $linktype);
	}

	public function processAction($funcname, &$controller, $params)
	{
		$callmanagementclass = Telephony::getDriverName() . '_cm' . $funcname . '_Driver';
				try {
				$callmanagementobject = new $callmanagementclass();
		} catch (Exception $e) {
				throw new callmanagerException('Unable to find driver for ' . $funcname);
		}
		return $callmanagementobject->processAction($controller, $params);
	}
	
	public function executeAction($funcname, $params)
	{	
		$callmanagementclass = Telephony::getDriverName() . '_cm' . $funcname . '_Driver';
		try {
				$callmanagementobject = new $callmanagementclass();
		} catch (Exception $e) {
				throw new callmanagerException('Unable to find driver for ' . $funcname);
		}
		return $callmanagementobject->executeAction($params);
	}
	
	private function _throwError($errorMessage, $errorLevel = -10)
	{
		throw new callmanagerException($errorMessage, $errorLevel);
	}
}
?>