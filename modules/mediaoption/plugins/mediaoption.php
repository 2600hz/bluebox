<?php defined('SYSPATH') or die('No direct access allowed.');

class MediaOption_Plugin extends Bluebox_Plugin
{
    protected $name = 'mediaoption';

    protected function viewSetup()
    {
        if (!parent::viewSetup())
        {
            return FALSE;
        }

        $this->subview->render_conditional = array(
            'qtipAjaxForm' => FALSE,
            'ajax' => FALSE
        );

        return TRUE;
    }
}