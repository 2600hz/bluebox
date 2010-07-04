<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('CallerId', 'conditioning');
    

    plugins::register('devicemanager/create', 'view', array('CallerId_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('CallerId_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('CallerId_Plugin', 'save'));


    plugins::register('contextmanager/create', 'view', array('CallerId_Plugin', 'update'));

    plugins::register('contextmanager/edit', 'view', array('CallerId_Plugin', 'update'));

    plugins::register('contextmanager', 'save', array('CallerId_Plugin', 'save'));