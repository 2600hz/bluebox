<?php

class ExternalXfer_Plugin extends Bluebox_Plugin {
    public function selector() {
        $subview = new View('externalxfer/selector');
        $subview->section = 'ExternalXfer';

        // Add our view to the main application
        $this->views[] = $subview;

    }
}