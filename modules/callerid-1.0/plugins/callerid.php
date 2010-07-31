<?php defined('SYSPATH') or die('No direct access allowed.');

class CallerId_Plugin extends Bluebox_Plugin
{
    protected $name = 'callerid';

    public function viewSetup()
    {
        $this->subview = new View('callerid/update');
        
        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }

    public function update_ext()
    {
        if (!$this->viewSetup())
        {
            return FALSE;
        }

        if (!$this->loadViewData())
        {
            return FALSE;
        }

        $this->subview->hide_internal = TRUE;

        if (!$this->addSubView())
        {
            return FALSE;
        }
    }
}
