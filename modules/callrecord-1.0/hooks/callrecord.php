<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('CallRecord', 'conditioning');

    dialplan::register('CallRecord', 'preanswer');

    dialplan::register('CallRecord', 'prenumber');


    plugins::register('devicemanager/create', 'view', array('CallRecord_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('CallRecord_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('CallRecord_Plugin', 'save'));

    plugins::register('xmlcdr/details', 'view', array('CallRecord_Plugin', 'recordings'));

