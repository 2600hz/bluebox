<?php defined('SYSPATH') or die('No direct access allowed.');

class SipEncryption_Plugin extends Bluebox_Plugin
{
    protected $name = 'sipencryption';

    protected function viewSetup()
    {
        $this->subview = new View('sipencryption/sipinterface');
        $this->subview->tab = 'main';
        $this->subview->section = 'general';
        return TRUE;
    }
}

?>