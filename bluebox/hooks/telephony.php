<?php defined('SYSPATH') or die('No direct access allowed.');

class TelephonyPlugin {
    public function bootstrapTelephony()
    {
        // Enable writing to switch configuration files each time a database table/field is changed
        if (Kohana::config('telephony.diskoutput')) {
            Doctrine_Manager::getInstance()->addListener(new TelephonyListener());
        }
    }
}

// start doctrine up so controller can access the models
Event::add('system.ready', array('TelephonyPlugin', 'bootstrapTelephony'));
