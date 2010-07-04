<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Bluebox
 * @author     K Anderson
 * @license    MPL
 */
class View extends View_Core {

    public static $controller = '';

    public static $method = '';

    public static $instance = NULL;

    public function render($print = FALSE, $renderer = FALSE)
    {
        if(isset($this->kohana_local_data['pluginName']))
            self::$controller = $this->kohana_local_data['pluginName'];
        else
            self::$controller = Router::$controller;

        if(isset($this->kohana_local_data['eventName']))
            self::$method = $this->kohana_local_data['eventName'];
        else
            self::$method = Router::$method;

        // Give helpers an entry to this view instance
        self::$instance = $this;

        return parent::render($print, $renderer);
    }
}
