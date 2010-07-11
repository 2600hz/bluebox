<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('devicemanager/create', 'view', array('Sip_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('Sip_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('Sip_Plugin', 'validate'));

    plugins::register('devicemanager', 'save', array('Sip_Plugin', 'save'));