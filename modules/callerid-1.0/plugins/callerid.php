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

    protected function validate($data, $validator)
    {
        if (isset($data['internal_number']))
        {
            if (preg_match('/[^0-9]/', $data['internal_number']))
            {
                $validator->add_error('callerid[internal_number]', 'Please provide only numbers');
            }
        }

        if (isset($data['external_number']))
        {
            if (preg_match('/[^0-9]/', $data['external_number']))
            {
                $validator->add_error('callerid[external_number]', 'Please provide only numbers');
            }
        }

        return TRUE;
    }
}