<?php defined('SYSPATH') or die('No direct access allowed.');
    
    Event::add('bluebox.maintenance', array('Call', 'importLogs'));
    