<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('locationmanager/create', 'view', array('Address_Plugin', 'update'));

    plugins::register('locationmanager/edit', 'view', array('Address_Plugin', 'update'));
    
    plugins::register('locationmanager', 'save', array('Address_Plugin', 'save'));