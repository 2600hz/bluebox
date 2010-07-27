<?php defined('SYSPATH') or die('No direct access allowed.');

class Moh_Plugin extends Bluebox_Plugin
{
    protected $name = 'moh';

    /**
     * This plugin is not reliant on data (yet)
     * @return <type>
     */
    protected function loadViewData() {
        return TRUE;
    }


}
