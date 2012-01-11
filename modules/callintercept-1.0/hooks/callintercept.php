<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('devicemanager/create', 'view', array('CallIntercept_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('CallIntercept_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('CallIntercept_Plugin', 'save'));

    dialplan::register('CallIntercept', 'prenumber');
