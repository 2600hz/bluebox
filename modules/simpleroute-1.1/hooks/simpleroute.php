<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('trunkmanager/create', 'view', array('SimpleRoute_Plugin', 'update'));

    plugins::register('trunkmanager/edit', 'view', array('SimpleRoute_Plugin', 'update'));

    plugins::register('trunkmanager', 'save', array('SimpleRoute_Plugin', 'save'));

    Event::add('bluebox.save_prepare', array('SimpleRouteLib', 'importRoutesNewAccount'));