<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('contextmanager/create', 'view', array('SimpleRoute_Plugin', 'update'));

    plugins::register('contextmanager/edit', 'view', array('SimpleRoute_Plugin', 'update'));

    plugins::register('contextmanager', 'save', array('SimpleRoute_Plugin', 'save'));

    plugins::register('accountmanager', 'delete', array('SimpleRoute_Plugin', 'buildAccountRelationships'));

    Event::add('bluebox.save_prepare', array('SimpleRouteLib', 'importRoutesNewAccount'));

    plugins::register('devicemanager/create', 'view', array('SimpleRoute_Plugin', 'device_update'));
    plugins::register('devicemanager/edit', 'view', array('SimpleRoute_Plugin', 'device_update'));
#    plugins::register('devicemanager', 'save', array('SimpleRoute_Plugin', 'device_save'));
