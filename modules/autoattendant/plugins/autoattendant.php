<?php

class AutoAttendant_Plugin extends FreePbx_Plugin {
    public function selector() {
        $subview = new View('autoattendant/selector');
        $subview->section = 'autoattendant';

        // Add our view to the main application
        $autoattendants = Doctrine_Query::create()
            ->select('auto_attendant_id, name')
            ->from('AutoAttendant')
            ->orderBy('name')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

         if (empty($autoattendants)) {
            return FALSE;
        }

       $subview->autoAttendants = array();
        foreach ($autoattendants as $autoattendant) {
            $subview->autoAttendants[$autoattendant['auto_attendant_id']] = $autoattendant['name'];
        }

        $this->views[] = $subview;

    }
}