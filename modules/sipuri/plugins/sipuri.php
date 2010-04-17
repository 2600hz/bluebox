<?php

class SipUri_Plugin extends FreePbx_Plugin {
    public function selector() {
        $subview = new View('sipuri/selector');
        $subview->section = 'SipUri';

        // Add our view to the main application
        $subview->failback_number = '1234';
        $subview->failback_context = NULL;
        $this->views[] = $subview;

    }
}