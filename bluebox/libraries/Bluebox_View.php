<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/View
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class View extends View_Core {

    public static $instance = NULL;
    
    public function render($print = FALSE, $renderer = FALSE)
    {
        // Give helpers an entry to this view instance
        self::$instance = $this;

        if ($this->is_set('mustache_template') or (stristr($this->kohana_filename, '.mus')))
        {
            if (isset($this->kohana_local_data['mustache_template']) and $this->kohana_local_data['mustache_template'] === FALSE)
            {
                return parent::render($print, $renderer);
            }

            $mustache_data = arr::merge(self::$kohana_global_data, $this->kohana_local_data);

            if (empty($this->kohana_local_data['mustache_partials']))
            {
                $mustache_partials = array();
            } 
            else
            {
                $mustache_partials = $this->kohana_local_data['mustache_partials'];
                
                unset($mustache_data['mustache_partials']);
            }

            $mustache = new Mustache();

            $output = $mustache->render(parent::render(FALSE), $mustache_data, $mustache_partials);

            $output = str_replace(array("\n", '  '), '', $output);

            if (!empty($this->kohana_local_data['mustache_escape_apostrophes']))
            {
                $output = str_replace('\'', '\\\'', $output);
            }

            if ($print === TRUE)
            {
                // Display the output
                echo $output;
                
                return;
            }

            return $output;
            
        } else {

            return parent::render($print, $renderer);

        }
    }
}