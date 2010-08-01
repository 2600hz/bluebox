<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of provisioner
 *
 * @author dman
 */
class Provisioner_Controller extends Bluebox_Controller {
    public function configure()
    {
        // LOOKUP THE RIGHT PICTURE & INFO TO SHOW


        if (isset($_POST['submit'])) {
            // SAVE BUTTON MAPPINGS
            $this->returnQtipAjaxForm();
        }
    }

    public function generate($mac)
    {
        
    }
}
