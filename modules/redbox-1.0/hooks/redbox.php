<?php defined('SYSPATH') or die('No direct access allowed.');

	plugins::register('devicemanager/create', 'view', array('Redbox_Plugin', 'update'));

	plugins::register('devicemanager/edit', 'view', array('Redbox_Plugin', 'update'));
