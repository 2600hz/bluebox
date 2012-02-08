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
						throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
					}

					$driverclass = 'FreeSwitch_ftr' . $obj['ftr_name'] . '_Driver';
					try {
						$driver = new $driverclass();
					} catch (Exception $e) {
						throw new featureException('Unable to find feature class ' . $driverlass);
					}
					$driver->set($obj);
				} catch (Package_Catalog_Exception $e) {
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
						throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
					}

					$driverclass = 'FreeSwitch_ftr' . $obj['ftr_name'] . '_Driver';
					try {
						$driver = new $driverclass();
					} catch (Exception $e) {
						throw new featureException('Unable to find feature class ' . $driverlass);
					}
					$driver->delete($obj);
				} catch (Package_Catalog_Exception $e) {
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
						throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
					}
					$driverclass = 'FreeSwitch_ftr' . $destination['ftr_name'] . '_Driver';
					try {
						$driver = new $driverclass();
					} catch (Exception $e) {
						throw new featureException('Unable to find feature class ' . $driverlass);
					}
					$driver->dialplan($number);
				} catch (Package_Catalog_Exception $e) {
					throw new featureException('Package ' . $packageobj->name . ' not found.', -10);
				}
			else
			{
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
