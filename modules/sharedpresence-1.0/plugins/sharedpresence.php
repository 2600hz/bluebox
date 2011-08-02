<?php defined('SYSPATH') or die('No direct access allowed.');

class SharedPresence_Plugin extends Bluebox_Plugin
{
    protected $name = 'sharedpresence';

    protected function viewSetup()
    {
        $this->subview = new View('sharedpresence/sipinterface');
        $this->subview->tab = 'main';
        $this->subview->section = 'general';
        return TRUE;
    }
}

?>