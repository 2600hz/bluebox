<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Welcome
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
class Welcome_Controller extends Bluebox_Controller {

    public function index()
    {
        $this->view->product = Kohana::config('core.product_name');

        plugins::views($this);
    }
}