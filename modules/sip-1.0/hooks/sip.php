<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('trunkmanager', 'view', array('Sip_Plugin', 'provideTrunkType'));

    plugins::register('trunkmanager/create', 'view', array('Sip_Plugin', 'updateTrunk'));

    plugins::register('trunkmanager/edit', 'view', array('Sip_Plugin', 'updateTrunk'));

    plugins::register('trunkmanager', 'save', array('Sip_Plugin', 'save'));


    plugins::register('devicemanager/create', 'view', array('Sip_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('Sip_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('Sip_Plugin', 'validate'));

    plugins::register('devicemanager', 'save', array('Sip_Plugin', 'save'));