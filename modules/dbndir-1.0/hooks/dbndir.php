<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('devicemanager/create', 'view', array('Dbndir_Plugin', 'update'));
    plugins::register('devicemanager/edit', 'view', array('Dbndir_Plugin', 'update'));
    plugins::register('devicemanager', 'save', array('Dbndir_Plugin', 'save'));

    plugins::register('numbermanager', 'save', array('Dbndir_Plugin', 'save'));
    
    Event::add('numbermanager.collectNumberOptions', array('Dbndir_Plugin', 'provideNumberOptions'));

    Event::add('numbermanager.collectNumberTargets', array('Dbndir_Plugin', 'provideNumberTargets'));
?>
