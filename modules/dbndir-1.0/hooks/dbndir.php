<?php defined('SYSPATH') or die('No direct access allowed.');
   dialplan::register('dbndir', 'prenumber');

    plugins::register('devicemanager/create', 'view', array('Dbndir_Plugin', 'update'));
    plugins::register('devicemanager/edit', 'view', array('Dbndir_Plugin', 'update'));
    plugins::register('devicemanager', 'save', array('Dbndir_Plugin', 'save'));

    Event::add('numbermanager.collectNumberTargets', array('Dbndir_Plugin', 'provideNumberTargets'));
?>
