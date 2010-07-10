<?php defined('SYSPATH') or die('No direct access allowed.');

class SkinsPlugin
{
    public function setup()
    {
        Event::$data->viewParams['folder'] = '../../' . skins::getSkin();
    }
}

// Hook to the creation of a html template
Event::add('bluebox.createtemplate.html', array('SkinsPlugin', 'setup'));
