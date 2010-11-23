<?php defined('SYSPATH') or die('No direct access allowed.');

class QuickAdd_Controller extends Bluebox_Controller
{
    protected $baseModel = 'QuickAdd';

    public function index()
    {
        $this->template->content = new View('generic/blank');
    }
}