<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('accountmanager/create', 'view', array('LocationManager_Plugin', 'initialAccountLocation'));

    plugins::register('accountmanager/edit', 'view', array('LocationManager_Plugin', 'index'));