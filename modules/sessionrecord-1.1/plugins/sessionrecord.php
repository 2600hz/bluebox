<?php defined('SYSPATH') or die('No direct access allowed.');

class SessionRecord_Plugin extends Bluebox_Plugin
{
    protected $name = 'sessionrecord';

    public function viewSetup()
    {
        $this->subview = new View('sessionrecord/update');
        
        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }

}
