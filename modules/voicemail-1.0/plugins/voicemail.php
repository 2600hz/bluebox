<?php defined('SYSPATH') or die('No direct access allowed.');

class Voicemail_Plugin extends Bluebox_Plugin
{
    protected $name = 'voicemail';

    protected function viewSetup()
    {
        $this->subview = new View('voicemail/associate');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }
}