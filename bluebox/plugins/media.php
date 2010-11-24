<?php defined('SYSPATH') or die('No direct access allowed.');

class Media_Plugin extends Bluebox_Plugin
{
    protected $name = 'media';
    
    protected static $components = array();

    protected function viewSetup()
    {
        $this->subview = new View('media/update');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        $data = Event::$data;

        Event::run('bluebox.media.widget');

        Event::$data = $data;

        if (self::$components)
        {
            $this->subview->set('components', self::$components);
        }

        return FALSE;
    }

    public static function addComponent($name, $view)
    {
        self::$components[$name] = $view;
    }
}
