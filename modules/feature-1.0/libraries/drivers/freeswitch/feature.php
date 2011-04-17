<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Feature_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj)
    {
		if (get_called_class() != 'FreeSwitch_Feature_Driver')
			throw new featureException('Subclass of Freeswitch Feature Driver called but the set function was not overwritten', -99);
        $registry = (array)$obj['registry'];

		if ($obj['ftr_package_id'] != 0)
		{
			$packageobj = Doctrine::getTable('package')->find($obj['ftr_package_id']);
			if ($packageobj)
				try {
					if (!$package = Package_Catalog::getInstalledPackage($packageobj->name))
					{
						Kohana::Log('debug', 'Package ' . $packageobj->name . ' not found.');
						throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
					}

					$driverfile = $package['directory'] . '/libraries/drivers/freeswitch/' . $obj['ftr_name'] . '.php';
					kohana::Log('debug', 'Looking for driver ' . $driverfile);
					if (!file_exists($driverfile))
					{
						kohana::Log('debug', 'Driver not found.');
					} else {
						kohana::Log('debug', 'Driver found.');
						include_once $driverfile;
						$driverclass = $obj['ftr_name'] . '_FreeSwitch_Feature_Driver';
						try {
							$driver = new $driverclass();
						} catch (Exception $e) {
							throw new featureException('Unable to find feature class ' . $driverlass);
						}
						$driver->set($obj);
					}
				} catch (Package_Catalog_Exception $e) {
					kohana::Log('debug', 'Package ' . $packageobj->name . ' not found.');
					throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
				}
		}
    }

    public static function delete($obj)
    {
		if (get_called_class() != 'FreeSwitch_Feature_Driver')
			throw new featureException('Subclass of Freeswitch Feature Driver called but the delete function was not overwritten', -99);
        $registry = (array)$obj['registry'];

		if ($obj['ftr_package_id'] != 0)
		{
			$packageobj = Doctrine::getTable('package')->find($obj['ftr_package_id']);
			if ($packageobj)
				try {
					if (!$package = Package_Catalog::getInstalledPackage($packageobj->name))
					{
						Kohana::Log('debug', 'Package ' . $packageobj->name . ' not found.');
						throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
					}

					$driverfile = $package['directory'] . '/libraries/drivers/freeswitch/' . $obj['ftr_name'] . '.php';
					kohana::Log('debug', 'Looking for driver ' . $driverfile);
					if (!file_exists($driverfile))
					{
						kohana::Log('debug', 'Driver not found.');
					} else {
						kohana::Log('debug', 'Driver found.');
						include $driverfile;
						$driverclass = $obj['ftr_name'] . '_FreeSwitch_Feature_Driver';
						try {
							$driver = new $driverclass();
						} catch (Exception $e) {
							throw new featureException('Unable to find feature class ' . $driverlass);
						}

						$driver->delete($obj);
					}
				} catch (Package_Catalog_Exception $e) {
					kohana::Log('debug', 'Package ' . $packageobj->name . ' not found.');
					throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
				}
		}
    }

    public static function dialplan($number)
    {
		if (get_called_class() != 'FreeSwitch_Feature_Driver')
			throw new featureException('Subclass of Freeswitch Feature Driver called but the Dialplan function was not overwritten', -99);
        $destination = $number['Destination'];
        $registry = (array)$destination['registry'];

		if ($destination['ftr_package_id'] != 0)
		{
			$packageobj = Doctrine::getTable('package')->find($destination['ftr_package_id']);
			if ($packageobj)
				try {
					if (!$package = Package_Catalog::getInstalledPackage($packageobj->name))
					{
						Kohana::Log('debug', 'Package ' . $packageobj->name . ' not found.');
						throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
					}
					$driverfile = $package['directory'] . '/libraries/drivers/freeswitch/' . $destination['ftr_name'] . '.php';
					kohana::Log('debug', 'Looking for driver ' . $driverfile);
					if (!file_exists($driverfile))
					{
						kohana::Log('debug', 'Driver not found.');
					} else {
						include_once $driverfile;
						$driverclass = $destination['ftr_name'] . '_FreeSwitch_Feature_Driver';
						kohana::Log('debug', 'Using feature driver: ' . $driverclass);
						try {
							$driver = new $driverclass();
						} catch (Exception $e) {
							throw new featureException('Unable to find feature class ' . $driverlass);
						}

						$driver->dialplan($number);
					}
				} catch (Package_Catalog_Exception $e) {
					kohana::Log('debug', 'Package ' . $packageobj->name . ' not found.');
					throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
				}
			else
			{
				kohana::Log('debug', 'Package ' . $destination['ftr_package_id'] . 'Found...');
				throw new featureException('Package ' . $destination['ftr_package_id'] . 'Found...', -10);
			}
		}
	}

    public function postRoute() {
    {
        $xml = FreeSWITCH::createExtension('global');
        // This XML code will track the number dialed, the caller ID of the last inbound call and/or some other basic info
        $newXml = <<<XML

  <condition>
    <action application="hash" data="insert/\${domain_name}-spymap/\${caller_id_number}/\${uuid}"/>
    <action application="hash" data="insert/\${domain_name}-last_dial/\${caller_id_number}/\${destination_number}"/>
    <action application="hash" data="insert/\${domain_name}-last_dial/global/\${uuid}"/>
    <action application="set" data="RFC2822_DATE=\${strftime(%a, %d %b %Y %T %z)}"/>
  </condition>

XML;
        $xml->replaceWithXml($newXml);
    }

    }
}
