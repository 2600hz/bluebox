<?php

class Conference_Plugin extends FreePbx_Plugin {
    public function selector() {
        $subview = new View('conference/selector');
        $subview->section = 'conference';

        // Add our view to the main application
        $conferences = Doctrine_Query::create()
            ->select('conference_id, name')
            ->from('Conference')
            ->orderBy('name')
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        if (empty($conferences)) {
            return FALSE;
        }

        $subview->conferences = array();
        foreach ($conferences as $conference) {
            $subview->conferences[$conference['conference_id']] = $conference['name'];
        }

        $this->views[] = $subview;

    }
}