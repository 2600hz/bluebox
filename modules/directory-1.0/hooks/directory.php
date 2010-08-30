<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('devicemanager/create', 'view', array('Directory_Plugin', 'update'));
    plugins::register('devicemanager/edit', 'view', array('Directory_Plugin', 'update'));
    plugins::register('devicemanager', 'save', array('Directory_Plugin', 'save'));
