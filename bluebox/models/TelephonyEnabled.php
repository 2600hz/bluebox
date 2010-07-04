<?php defined('SYSPATH') or die('No direct access allowed.');

class TelephonyEnabled extends Doctrine_Template
{
    public function setUp()
    {
        // LISTENERS

        // Only add if we are saving immediately
        if (Kohana::config('telephony.diskoutput'))
            $this->addListener(new TelephonyRecordListener());  // This allows us to write to a config file each time this table changes
    }
}
