<?php
/**
 * Destinations selection class
 *
 * @author dschreiber
 */
class Destinations_Controller extends FreePbx_Controller {
    public function selector($dom_id = NULL, $numberId = NULL, $allowedTypes = NULL) {
        $dummy = NULL;
        
        plugins::views($this);

        $this->view->dom_id = $dom_id;
    }
}
