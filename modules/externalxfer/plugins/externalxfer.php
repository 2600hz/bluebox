<?php

class ExternalXfer_Plugin extends FreePbx_Plugin {
    public function selector() {
        $subview = new View('externalxfer/selector');
        $subview->section = 'ExternalXfer';

        $tmp = Doctrine::getTable('Trunk')->findAll(Doctrine::HYDRATE_ARRAY);
        $subview->trunks = array();
        foreach ($tmp as $trunk) {
            $subview->trunks[$trunk['trunk_id']] = $trunk['name'];
        }

        if (class_exists('SipInterface', TRUE)) {
            $tmp = Doctrine::getTable('SipInterface')->findAll(Doctrine::HYDRATE_ARRAY);
            $subview->interfaces = array();
            foreach ($tmp as $interface) {
                $subview->interfaces[$interface['sipinterface_id']] = $interface['name'];
            }
        }

        // Add our view to the main application
        $this->views[] = $subview;

    }
}