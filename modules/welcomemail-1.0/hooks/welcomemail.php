<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('devicemanager/create', 'view', array('Welcomemail_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('Welcomemail_Plugin', 'update'));
    
    plugins::register('devicemanager', 'save', array('Welcomemail_Plugin', 'save'));
