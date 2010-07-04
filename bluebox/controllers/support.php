<?php defined('SYSPATH') or die('No direct access allowed.');

class Support_Controller extends Bluebox_Controller {

    public function request_help()
    {
        if ($this->submitted(NULL, array('submitString' => 'send'))) {
            message::set('Request was submitted, you will be contacted shortly.', 'success');
            $this->returnQtipAjaxForm(NULL, TRUE);
        }
    }
}
