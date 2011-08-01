<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('devicemanager/create', 'view', array('Provisioner_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('Provisioner_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('Provisioner_Plugin', 'save'));
