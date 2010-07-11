<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('usermanager', 'save', array('UserManager_Plugin', 'validate'));

    // Add a hook for managing users within the device manager
    plugins::register('devicemanager/index', 'view', array('UserManager_Plugin', 'appendToGrid'));

    plugins::register('locationmanager/edit', 'view', array('UserManager_Plugin', 'createSubGrid'));

    plugins::register('accountmanager/create', 'view', array('UserManager_Plugin', 'initialAccountUsers'));
    
    plugins::register('accountmanager/edit', 'view', array('UserManager_Plugin', 'createSubGrid'));