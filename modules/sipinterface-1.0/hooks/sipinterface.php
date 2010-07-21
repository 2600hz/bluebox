<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('trunkmanager', 'view', array('SipInterface_Plugin', 'update'));
    
    plugins::register('trunkmanager', 'save', array('SipInterface_Plugin', 'save'));
