<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('trunkmanager/create', 'view', array('SipInterface_Plugin', 'update'));
    
    plugins::register('trunkmanager/edit', 'view', array('SipInterface_Plugin', 'update'));

    plugins::register('trunkmanager', 'save', array('SipInterface_Plugin', 'save'));


    plugins::register('locationmanager/create', 'view', array('SipInterface_Plugin', 'update'));

    plugins::register('locationmanager/edit', 'view', array('SipInterface_Plugin', 'update'));

    plugins::register('locationmanager', 'save', array('SipInterface_Plugin', 'save'));