<?php defined('SYSPATH') or die('No direct access allowed.');

class Welcome_Controller extends Bluebox_Controller {

    public function index()
    {
        $this->view->product = Kohana::config('core.product_name');
    }
}
