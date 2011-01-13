<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('accountmanager/create', 'view', array('LocationManager_Plugin', 'initialAccountLocation'));

    plugins::register('accountmanager/edit', 'view', array('LocationManager_Plugin', 'index'));

    plugins::register('devicemanager', 'save', array('LocationManager_Plugin', 'save'));

    plugins::register('locationmanager', 'save', array('LocationManager_Plugin', 'save'));

    Event::add('bluebox.create.extension', array('LocationManager', 'updateAreacode'));