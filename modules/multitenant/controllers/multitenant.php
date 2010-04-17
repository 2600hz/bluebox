<?php
/**
 * Description of multitenant
 *
 * @author dschreiber
 */
class MultiTenant extends FreePbx_Controller {
    public function index() {
        $this->view->interfaces = array();
        $this->view->interfaces[] = array('Name' => 'Primary Box', 'DNS' => 'fred.somehost.com', 'IP1' => '216.82.223.1');
        $this->view->interfaces[] = array('Name' => 'Backup Box', 'DNS' => 'wilma.somehost.com', 'IP1' => '216.82.235.9');

    }
}
