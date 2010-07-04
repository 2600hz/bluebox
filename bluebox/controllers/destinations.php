<?php defined('SYSPATH') or die('No direct access allowed.');

class Destinations_Controller extends Bluebox_Controller {
    public function selector($dom_id = NULL, $numberId = NULL, $allowedTypes = NULL) {
        $dummy = NULL;
        
        plugins::views($this);

        $this->view->dom_id = $dom_id;
    }
}
