<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('accountmanager/create', 'view', array('LocationManager_Plugin', 'initialAccountLocation'));

    plugins::register('accountmanager/edit', 'view', array('LocationManager_Plugin', 'index'));

    //Event::add('bluebox.save', array('LocationManager', 'setAreacode'));

    plugins::register('devicemanager', 'save', array('LocationManager_Plugin', 'save'));

    plugins::register('locationmanager', 'save', array('LocationManager_Plugin', 'save'));