<?php defined('SYSPATH') or die('No direct access allowed.');

    // Add SIP device fields to the page where devices are added
    plugins::register('devicemanager/create', 'view', array('MediaOption_Plugin', 'update'));
    
    plugins::register('devicemanager/edit', 'view', array('MediaOption_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('MediaOption_Plugin', 'save'));

    // Add SIP device fields to the page where devices are added
    plugins::register('contextmanager/create', 'view', array('MediaOption_Plugin', 'update'));

    plugins::register('contextmanager/edit', 'view', array('MediaOption_Plugin', 'update'));

    plugins::register('contextmanager', 'save', array('MediaOption_Plugin', 'save'));