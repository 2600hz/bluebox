<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('accountmanager/create', 'view', array('UserManager_Plugin', 'initialAccountUsers'));

    plugins::register('accountmanager/edit', 'view', array('UserManager_Plugin', 'createSubGrid'));

    plugins::register('devicemanager/index', 'view', array('UserManager_Plugin', 'appendToGrid'));

    plugins::register('locationmanager/edit', 'view', array('UserManager_Plugin', 'createSubGrid'));