<?php

class DeviceManager_Plugin extends FreePbx_Plugin {
    public function selector() {
        $subview = new View('devicemanager/selector');
        $subview->section = 'devices';

        // Add our view to the main application
        $devices = Doctrine_Query::create()
            ->select('device_id, name')
            ->from('Device')
            ->orderBy('name')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        if (empty($devices)) {
            return FALSE;
        }

        $subview->devices = array();
        foreach ($devices as $device) {
            $subview->devices[$device['device_id']] = $device['name'];
        }

        $subview->fallback_number = '1234';
        $subview->fallback_context = NULL;
        $this->views[] = $subview;

    }
}