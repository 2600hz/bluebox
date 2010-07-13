<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 */
class Provisioner_Plugin extends Bluebox_Plugin
{
   protected $driver = NULL;

    public function refreshFiles()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();

        if (empty($base['device_id']))
            return TRUE;	// Nothing to do here.

        // get the endpoints that use this device
        $endpointLines = Doctrine::getTable('EndpointLine')->findByDeviceId($base['device_id']);
        if (!empty($endpointLines)) {
            foreach ($endpointLines as $endpointLine) {
                try {
                    // attempt to get the driver
                    $driver = EndpointManager::getDriverById($endpointLine['endpoint_id']);
                } catch (Exception $e) {
                    // if there is no driver by that ID it will throw, so move on
                    kohana::log('info', $e->getMessage());
                    continue;
                }
                kohana::log('debug', 'Updating ' . get_class($driver) . ' provisioning files...');

                // delete any provisioning files for this endpoint
                $driver->deleteFiles();

                // re-create the provisioning files
                $driver->createFiles();
            }
        }

        return TRUE;
    }

    public function removeLine()
    {
        // What are we working with here?
        $base = Event::$data;

        if (empty($base['device_id']))
            return FALSE;	// Nothing to do here.

        // get the endpoints that use this device
        $endpointLines = Doctrine::getTable('EndpointLine')->findByDeviceId($base['device_id']);
        if (!empty($endpointLines)) {
            foreach ($endpointLines as $endpointLine) {

                $endpointId = $endpointLine['endpoint_id'];

                // get the driver, or load it if we havent already used it
                if (!empty($this->driver) && array_key_exists($endpointId, $this->driver)) {
                    $driver = $this->driver[$endpointId];
                } else {
                    try {
                        $driver = EndpointManager::getDriverById($endpointLine['endpoint_id']);
                        $this->driver[$endpointId] = $driver;
                    } catch (Exception $e) {
                        // if there is no driver by that ID it will throw, so move on
                        kohana::log('info', $e->getMessage());
                        continue;
                    }
                }

                // cache the files to be deleted when this delete transaction completes
                $driver->deleteFiles(TRUE);

                // mark the endpointLine for deletion
                $endpointLine->delete();
            }
        }
        
    }

    public function successfulDelete ()
    {
        if (!empty($this->driver)) {
            foreach ($this->driver as $endpointId => $driver) {
                $driver->refresh();
                $driver->createFiles();
            }
            $this->driver = NULL;
        }
    }

    public function removeAllLines()
    {
        // What are we working with here?
        $base = Event::$data;

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (empty($base))
            return FALSE;	// Nothing to do here.

        // make sure there is nothing left here
        $this->driver = NULL;
        try {
            $driver = EndpointManager::getDriverById($base['endpoint_id']);
            $driver->deleteFiles(TRUE, FALSE, TRUE);
            $this->driver[$base['endpoint_id']] = $driver;
        } catch (Exception $e) {
            kohana::log('error', $e->getMessage());
        }

        foreach ($base->EndpointLine as $endpointLine) {
            $endpointLine->delete();
        }
    }

    public function removeFiles()
    {
        if (!empty($this->driver)) {
            foreach ($this->driver as $endpointId => $driver) {
                $driver->deletePending();
            }
            $this->driver = NULL;
        }
    }
}

