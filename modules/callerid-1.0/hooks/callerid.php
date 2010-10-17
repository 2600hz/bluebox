<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('CallerId', 'conditioning');

    dialplan::register('CallerId', 'prenumber');


    plugins::register('devicemanager/create', 'view', array('CallerId_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('CallerId_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('CallerId_Plugin', 'save'));


    plugins::register('externalxfer/create', 'view', array('CallerId_Plugin', 'update_ext'));

    plugins::register('externalxfer/edit', 'view', array('CallerId_Plugin', 'update_ext'));

    plugins::register('externalxfer', 'save', array('CallerId_Plugin', 'save'));

